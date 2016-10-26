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

use Fxp\Component\RequireAsset\Assetic\Config\AssetReplacementManagerInterface;
use Fxp\Component\RequireAsset\Twig\Extension\AssetExtension;
use Fxp\Component\RequireAsset\Twig\Node\RequireTagReference;

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
     * @var AssetReplacementManagerInterface|null
     */
    protected $replacementManager;

    /**
     * Constructor.
     *
     * @param AssetReplacementManagerInterface|null $replacementManager The asset replacement manager
     * @param string|null                           $extension          The class name of twig extension
     */
    public function __construct(AssetReplacementManagerInterface $replacementManager = null, $extension = null)
    {
        $this->replacementManager = $replacementManager;
        $this->extension = null !== $extension ? $extension : AssetExtension::class;
    }

    /**
     * Parses a token and returns a node.
     *
     * @param \Twig_Token $token A Twig_Token instance
     *
     * @return \Twig_NodeInterface A Twig_NodeInterface instance
     *
     * @throws \Twig_Error_Syntax When the twig tag does not have at least one asset
     */
    public function parse(\Twig_Token $token)
    {
        $name = uniqid($this->getTag());
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $assets = array();

        while ($stream->test(\Twig_Token::STRING_TYPE)) {
            $assets[] = $this->getRealAssetName($stream->next()->getValue());

            if ($stream->test(\Twig_Token::PUNCTUATION_TYPE)) {
                $stream->next();
            }
        }

        $attributes = $this->getTagAttributes();
        $position = $this->getPosition($attributes);

        if (empty($assets)) {
            throw new \Twig_Error_Syntax(sprintf('The twig tag "%s" require a lest one asset', $this->getTag()), $stream->getCurrent()->getLine(), $stream->getSourceContext()->getName());
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

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
