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
 * This is the class that validates and merges configuration for require style tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireStyleTagConfiguration extends AbstractConfiguration
{
    /**
     * {@inheritdoc}
     */
    public static function getNodeDefinition()
    {
        $node = static::createRoot('require_style')
            ->children()
            ->scalarNode('position')->defaultNull()->end()
            ->scalarNode('href')->defaultNull()->end()
            ->scalarNode('rel')->defaultValue('stylesheet')->end()
            ->scalarNode('media')->defaultNull()->end()
            ->scalarNode('type')->defaultNull()->end()
            ->scalarNode('hreflang')->defaultNull()->end()
            ->integerNode('sizes')->defaultNull()->end()
            ->end()
        ;

        return $node;
    }
}
