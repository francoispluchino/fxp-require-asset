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

use Fxp\Component\RequireAsset\Twig\Config\RequireScriptConfiguration;

/**
 * Token Parser for the 'require_script' tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireScriptTokenParser extends AbstractRequireAssetTokenParser
{
    /**
     * {@inheritDoc}
     */
    public function getTag()
    {
        return 'require_script';
    }

    /**
     * {@inheritDoc}
     */
    protected function getAttributeNodeConfig()
    {
        return RequireScriptConfiguration::getNode();
    }

    /**
     * {@inheritDoc}
     */
    protected function getTwigAssetClass()
    {
        return 'Fxp\Component\RequireAsset\Twig\Asset\RequireScriptTwigAsset';
    }
}
