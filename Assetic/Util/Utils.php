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
 * Assetic Utils.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class Utils
{
    /**
     * @var array
     */
    protected static $nameFilters = array(
        '.',
        '/',
        '\\',
        '=',
        '+',
        '-',
        '*',
        '#',
        '&',
        '@',
        ':',
    );

    /**
     * Gets the assetic name of the asset.
     *
     * @param string $name
     *
     * @return string
     */
    public static function formatName($name)
    {
        $asseticName = ltrim($name, '@');
        $asseticName = str_replace(self::$nameFilters, '_', $asseticName);

        return $asseticName;
    }

    /**
     * Merges the configs of asset config.
     *
     * @param array $configs The list of config of asset config.
     *
     * @return array The merged config
     */
    public static function mergeConfigs(array $configs)
    {
        $new = array();

        foreach ($configs as $config) {
            foreach (array_keys($config) as $key) {
                $value = $config[$key];

                if (is_array($value) && array_key_exists($key, $new)) {
                    $value = array_merge($new[$key], $value);
                    array_unique($value);
                }

                if (null !== $value || !array_key_exists($key, $new)) {
                    $new[$key] = $value;
                }
            }
        }

        return $new;
    }
}
