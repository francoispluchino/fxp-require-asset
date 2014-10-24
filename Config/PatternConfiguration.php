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
 * This is the class that validates and merges configuration for pattern.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class PatternConfiguration extends AbstractConfiguration
{
    /**
     * {@inheritdoc}
     */
    public static function getNodeDefinition()
    {
        $node = static::createRoot('patterns')
            ->prototype('scalar')->end()
        ;

        return $node;
    }
}
