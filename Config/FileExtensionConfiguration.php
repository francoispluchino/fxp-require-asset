<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Config;

/**
 * This is the class that validates and merges configuration for file extensions.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FileExtensionConfiguration extends AbstractConfiguration
{
    /**
     * {@inheritdoc}
     */
    public static function getNodeDefinition()
    {
        $node = static::createRoot('extensions')
            ->useAttributeAsKey('name', false)
            ->normalizeKeys(false)
            ->prototype('array')
                ->children()
                    ->scalarNode('name')->end()
                    ->arrayNode('filters')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('options')
                        ->useAttributeAsKey('name')
                        ->normalizeKeys(false)
                        ->prototype('variable')->end()
                    ->end()
                    ->scalarNode('extension')->defaultNull()->end()
                    ->booleanNode('debug')->defaultFalse()->end()
                    ->booleanNode('exclude')->defaultFalse()->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
