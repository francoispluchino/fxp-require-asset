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

use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionInterface;
use Fxp\Component\RequireAsset\Assetic\Factory\Config\FileExtensionFactory;

/**
 * File Extension Utils.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class FileExtensionUtils
{
    /**
     * Create the file extension with the config.
     *
     * @param string|array|FileExtensionInterface $name      The name of extension or config, or instance
     * @param array                               $options   The assetic formulae options
     * @param array                               $filters   The assetic formulae filters
     * @param string|null                         $extension The output extension
     * @param bool                                $debug     The debug mode
     * @param bool                                $exclude   Exclude or not the file extension
     *
     * @return FileExtensionInterface
     */
    public static function createByConfig($name, array $options, array $filters, $extension, $debug, $exclude)
    {
        if (!$name instanceof FileExtensionInterface) {
            $config = is_array($name) ? $name
                : array(
                    'name'      => $name,
                    'options'   => $options,
                    'filters'   => $filters,
                    'extension' => $extension === $name ? null : $extension,
                    'debug'     => $debug,
                    'exclude'   => $exclude,
                )
            ;

            $name = FileExtensionFactory::create($config);
        }

        return $name;
    }

    /**
     * Get the default config of file extensions.
     *
     * @return array
     */
    public static function getDefaultConfigs()
    {
        return array(
            'map'  => array('debug' => true),
            'js'   => array(),
            'css'  => array(),
            'eot'  => array(),
            'svg'  => array(),
            'ttf'  => array(),
            'woff' => array(),
            'jpg'  => array(),
            'jpeg' => array(),
            'png'  => array(),
            'gif'  => array(),
            'webp' => array(),
            'mp3'  => array(),
            'aac'  => array(),
            'wav'  => array(),
            'ogg'  => array(),
            'webm' => array(),
            'mp4'  => array(),
            'ogv'  => array(),
        );
    }
}
