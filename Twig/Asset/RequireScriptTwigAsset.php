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
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'script';
    }

    /**
     * {@inheritdoc}
     */
    public function shortEndTag()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlTag()
    {
        return 'script';
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkAttribute()
    {
        return 'src';
    }
}
