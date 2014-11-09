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

use Fxp\Component\RequireAsset\Twig\Node\RequireAssetReference;

/**
 * Abstract Token Parser for the 'require_ASSET' tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractRequireAssetTokenParser extends AbstractTokenParser
{
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
            $assets[] = $stream->next()->getValue();

            if ($stream->test(\Twig_Token::PUNCTUATION_TYPE)) {
                $stream->next();
            }
        }

        $attributes = $this->getTagAttributes();
        $position = $this->getPosition($attributes);

        if (empty($assets)) {
            throw new \Twig_Error_Syntax(sprintf('The twig tag "%s" require a lest one asset', $this->getTag()), $stream->getCurrent()->getLine(), $stream->getFilename());
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new RequireAssetReference($name, $this->getTwigAssetClass(), $assets, $attributes, $lineno, $position);
    }
}