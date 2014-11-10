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
 * Config of twig require style asset.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireStyleTwigAsset extends AbstractRequireTwigAsset
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'style';
    }

    /**
     * {@inheritdoc}
     */
    public function shortEndTag()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlTag()
    {
        return 'link';
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkAttribute()
    {
        return 'href';
    }
}
