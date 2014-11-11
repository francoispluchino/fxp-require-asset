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

use Fxp\Component\RequireAsset\Tag\Config\RequireScriptTagConfiguration;

/**
 * Token Parser for the 'require_script' tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireScriptTokenParser extends AbstractRequireTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'require_script';
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeNodeConfig()
    {
        return RequireScriptTagConfiguration::getNode();
    }

    /**
     * {@inheritdoc}
     */
    protected function getTagClass()
    {
        return 'Fxp\Component\RequireAsset\Tag\RequireScriptTag';
    }
}
