<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tag\Config;

use Fxp\Component\RequireAsset\Config\AbstractConfiguration;

/**
 * This is the class that validates and merges configuration for inline tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InlineTagConfiguration extends AbstractConfiguration
{
    /**
     * {@inheritdoc}
     */
    public static function getNodeDefinition()
    {
        $node = static::createRoot('inline_tag')
            ->children()
            ->scalarNode('position')->defaultNull()->end()
            ->booleanNode('keep_html_tag')->defaultFalse()->end()
            ->end()
        ;

        return $node;
    }
}
