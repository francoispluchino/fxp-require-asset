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

/**
 * Token Parser for the 'inline_script' tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InlineScriptTokenParser extends AbstractInlineAssetTokenParser
{
    /**
     * {@inheritDoc}
     */
    public function getTag()
    {
        return 'inline_script';
    }

    /**
     * {@inheritDoc}
     */
    protected function getTwigAssetClass()
    {
        return 'Fxp\Component\RequireAsset\Twig\Asset\InlineScriptTwigAsset';
    }
}
