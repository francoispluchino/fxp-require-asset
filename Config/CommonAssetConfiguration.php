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
 * This is the class that validates and merges configuration for common asset.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class CommonAssetConfiguration extends AbstractConfiguration
{
    /**
     * {@inheritdoc}
     */
    public static function getNodeDefinition()
    {
        $node = static::createRoot('common_assets')
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->beforeNormalization()
                    // a scalar is a simple formula of one input file
                    ->ifTrue(function ($v) {
                        return !is_array($v);
                    })
                    ->then(function ($v) {
                        return array(
                            'output' => '/'.trim($v, '@'),
                            'inputs' => array($v),
                        );
                    })
                ->end()
                ->beforeNormalization()
                    ->always()
                    ->then(function ($v) {
                        // cast scalars as array
                        foreach (array('input', 'inputs', 'filter', 'filters') as $key) {
                            if (isset($v[$key]) && !is_array($v[$key])) {
                                $v[$key] = array($v[$key]);
                            }
                        }

                        // organize arbitrary options
                        foreach ($v as $key => $value) {
                            if (!in_array($key, array('input', 'inputs', 'filter', 'filters', 'option', 'options', 'output'))) {
                                $v['options'][$key] = $value;
                                unset($v[$key]);
                            }
                        }

                        return $v;
                    })
                ->end()

                // the formula
                ->fixXmlConfig('input')
                ->fixXmlConfig('filter')
                ->children()
                    ->scalarNode('output')->isRequired()->end()
                    ->arrayNode('inputs')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('filters')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('options')
                        ->useAttributeAsKey('name')
                        ->prototype('variable')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
