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

use Fxp\Component\RequireAsset\Assetic\Config\ConfigInterface;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Definition\Processor;

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
     * @param NodeInterface $nodeConfiguration The node definition of configuration
     * @param array         $configs           The list of config of asset config.
     *
     * @return array The merged config
     */
    public static function mergeConfigs(NodeInterface $nodeConfiguration, array $configs)
    {
        $processor = new Processor();
        $config = $processor->process($nodeConfiguration, $configs);

        return current($config);
    }

    /**
     * Add field in array config.
     *
     * @param array           $value      The array config
     * @param string          $field      The field name
     * @param ConfigInterface $config     The config instance
     * @param string          $method     The method of config instance
     * @param bool            $comparison The comparison
     * @param bool            $force      Force to add the field
     * @param bool            $useNull    Replace value by null if the comparison is false
     */
    public static function addField(array &$value, $field, ConfigInterface $config, $method, $comparison, $force = false, $useNull = false)
    {
        if ($force || $comparison) {
            $fieldValue = $config->$method();
            $value[$field] = !$comparison && $useNull ? null : $fieldValue;
        }
    }

    /**
     * Add string filed in array config.
     *
     * @param array           $value  The array config
     * @param string          $field  The field name
     * @param ConfigInterface $config The config instance
     * @param string          $method The method of config instance
     * @param bool            $force  Force to add the field
     */
    public static function addStringField(array &$value, $field, ConfigInterface $config, $method, $force = false)
    {
        $comparison = null !== $config->$method();

        static::addField($value, $field, $config, $method, $comparison, $force);
    }

    /**
     * Add array filed in array config.
     *
     * @param array           $value      The array config
     * @param string          $field      The field name
     * @param ConfigInterface $config     The config instance
     * @param string          $method     The method of config instance
     * @param int             $validCount The valid count
     * @param bool            $force      Force to add the field
     */
    public static function addArrayField(array &$value, $field, ConfigInterface $config, $method, $validCount, $force = false)
    {
        $comparison = count($config->$method()) > $validCount;

        static::addField($value, $field, $config, $method, $comparison, $force);
    }

    /**
     * Add bool filed in array config.
     *
     * @param array           $value     The array config
     * @param string          $field     The field name
     * @param ConfigInterface $config    The config instance
     * @param string          $method    The method of config instance
     * @param bool            $validBool The valid bool
     * @param bool            $force     Force to add the field
     */
    public static function addBoolField(array &$value, $field, ConfigInterface $config, $method, $validBool, $force = false)
    {
        $comparison = $validBool === $config->$method();

        static::addField($value, $field, $config, $method, $comparison, $force);
    }
}
