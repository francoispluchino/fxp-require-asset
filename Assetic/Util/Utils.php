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
}
