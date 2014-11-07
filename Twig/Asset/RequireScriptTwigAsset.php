<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\Asset;

/**
 * Config of twig require script asset.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireScriptTwigAsset extends AbstractRequireTwigAsset
{
    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return 'script';
    }

    /**
     * {@inheritDoc}
     */
    protected function shortEndTag()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function getHtmlTag()
    {
        return 'script';
    }

    /**
     * {@inheritDoc}
     */
    protected function getLinkAttribute()
    {
        return 'src';
    }
}
