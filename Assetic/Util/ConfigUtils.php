<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic\Util;

/**
 * Assetic Config Utils.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class ConfigUtils
{
    /**
     * Add the configs in the manager.
     *
     * @param array  $configs  The configs
     * @param object $instance The instance
     * @param string $method   The method
     *
     * @return object The instance
     */
    public static function addConfig(array $configs, $instance, $method)
    {
        foreach ($configs as $key => $config) {
            if (is_array($config) && !isset($config['name'])) {
                $config['name'] = $key;
            }

            $instance->$method($config);
        }

        return $instance;
    }
}
