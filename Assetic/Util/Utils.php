<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Assetic\Util;

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
    protected static $formulaeFilters = array(
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
     * Gets the assetic formulae name of the asset.
     *
     * @param string $name
     *
     * @return string
     */
    public static function getFormulaeName($name)
    {
        $formulae = ltrim($name, '@');
        $formulae = str_replace(self::$formulaeFilters, '_', $formulae);

        return $formulae;
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
