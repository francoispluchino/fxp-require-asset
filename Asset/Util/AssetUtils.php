<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Asset\Util;

/**
 * Asset Utils.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AssetUtils
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
     * Gets the formatted name of the asset.
     *
     * @param string $name
     *
     * @return string
     */
    public static function formatName($name)
    {
        $assetName = ltrim($name, '@');
        $assetName = str_replace(self::$nameFilters, '_', $assetName);

        return $assetName;
    }
}
