<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\TokenParser;

use Fxp\Component\RequireAsset\Asset\Config\AssetReplacementManagerInterface;
use Fxp\Component\RequireAsset\Twig\Extension\AssetExtension;
use Fxp\Component\RequireAsset\Twig\Node\RequireTagReference;
use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Token;

/**
 * Abstract Token Parser for the 'require_Type' tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractRequireTokenParser extends AbstractTokenParser
{
    /**
     * @var string
     */
    protected $extension;

    /**
     * @var null|AssetReplacementManagerInterface
     */
    protected $replacementManager;

    /**
     * Constructor.
     *
     * @param null|AssetReplacementManagerInterface $replacementManager The asset replacement manager
     * @param null|string                           $extension          The class name of twig extension
     */
    public function __construct(AssetReplacementManagerInterface $replacementManager = null, $extension = null)
    {
        $this->replacementManager = $replacementManager;
        $this->extension = null !== $extension ? $extension : AssetExtension::class;
    }

    /**
     * Parses a token and returns a node.
     *
     * @param Token $token A Twig_Token instance
     *
     * @throws SyntaxError When the twig tag does not have at least one asset
     *
     * @return Node A Twig_Node instance
     */
    public function parse(Token $token)
    {
        $name = uniqid($this->getTag());
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $assets = [];

        while ($stream->test(Token::STRING_TYPE)) {
            $assets[] = $this->getRealAssetName($stream->next()->getValue());

            if ($stream->test(Token::PUNCTUATION_TYPE)) {
                $stream->next();
            }
        }

        $attributes = $this->getTagAttributes();
        $position = $this->getPosition($attributes);

        if (empty($assets)) {
            throw new SyntaxError(sprintf('The twig tag "%s" require a lest one asset', $this->getTag()), $stream->getCurrent()->getLine(), $stream->getSourceContext());
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return new RequireTagReference($this->extension, $name, $this->getTagClass(), $assets, $attributes, $lineno, $position);
    }

    /**
     * Get the real asset name.
     *
     * @param string $assetName The asset name required
     *
     * @return string
     */
    protected function getRealAssetName($assetName)
    {
        $prefix = 0 === strpos($assetName, '?') ? '?' : '';
        $assetName = ltrim($assetName, '?');

        if (null !== $this->replacementManager && $this->replacementManager->hasReplacement($assetName)) {
            $assetName = $this->replacementManager->getReplacement($assetName);
        }

        return $prefix.$assetName;
    }
}
