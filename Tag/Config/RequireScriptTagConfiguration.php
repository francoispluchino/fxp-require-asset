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
 * This is the class that validates and merges configuration for require script tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireScriptTagConfiguration extends AbstractConfiguration
{
    /**
     * {@inheritdoc}
     */
    public static function getNodeDefinition()
    {
        $node = static::createRoot('require_script')
            ->children()
                ->scalarNode('position')->defaultNull()->end()
                ->scalarNode('src')->defaultNull()->end()
                ->booleanNode('async')
                    ->defaultNull()
                    ->validate()
                        ->always()
                        ->then(function ($value) {
                            return $value ? 'async' : null;
                        })
                    ->end()
                ->end()
                ->booleanNode('defer')
                    ->defaultNull()
                    ->validate()
                        ->always()
                        ->then(function ($value) {
                            return $value ? 'defer' : null;
                        })
                    ->end()
                ->end()
                ->scalarNode('charset')->defaultNull()->end()
                ->scalarNode('type')->defaultNull()->end()
            ->end()
        ;

        return $node;
    }
}
