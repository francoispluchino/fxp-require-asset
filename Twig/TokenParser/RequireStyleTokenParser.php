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

use Fxp\Component\RequireAsset\Twig\Config\RequireStyleConfiguration;

/**
 * Token Parser for the 'require_style' tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireStyleTokenParser extends AbstractRequireAssetTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'require_style';
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeNodeConfig()
    {
        return RequireStyleConfiguration::getNode();
    }

    /**
     * {@inheritdoc}
     */
    protected function getTwigAssetClass()
    {
        return 'Fxp\Component\RequireAsset\Twig\Asset\RequireStyleTwigAsset';
    }
}
