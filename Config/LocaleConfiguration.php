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
 * This is the class that validates and merges configuration for locale asset.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class LocaleConfiguration extends AbstractConfiguration
{
    /**
     * {@inheritdoc}
     */
    public static function getNodeDefinition()
    {
        $node = static::createRoot('locales')
            ->useAttributeAsKey('locale', false)
            ->normalizeKeys(false)
            ->prototype('array')
                ->prototype('array')
                    ->normalizeKeys(false)
                    ->beforeNormalization()
                        // a scalar is a simple formula of one input file
                        ->ifTrue(function ($v) {
                            return is_string($v);
                        })
                        ->then(function ($v) {
                            return array($v);
                        })
                    ->end()
                    ->validate()
                    ->ifTrue(function ($v) {
                        return 0 === count($v);
                    })
                        ->thenInvalid('The localized asset must be present')
                    ->end()
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
