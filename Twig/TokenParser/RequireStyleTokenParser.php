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
 * Token Parser for the 'require_style' tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireStyleTokenParser extends AbstractRequireAssetTokenParser
{
    /**
     * {@inheritDoc}
     */
    public function getTag()
    {
        return 'require_style';
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultAttributes()
    {
        return array_merge(parent::getDefaultAttributes(), array(
            'href'     => null,
            'rel'      => 'stylesheet',
            'media'    => null,
            'type'     => null,
            'hreflang' => null,
            'sizes'    => null,
        ));
    }

    /**
     * {@inheritDoc}
     */
    protected function getTwigAssetClass()
    {
        return 'Fxp\Component\RequireAsset\Twig\Asset\RequireStyleTwigAsset';
    }
}
