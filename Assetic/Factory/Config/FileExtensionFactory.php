<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Assetic\Factory\Config;

use Fxp\Bundle\RequireAssetBundle\Assetic\Config\FileExtension;
use Fxp\Bundle\RequireAssetBundle\Assetic\Config\FileExtensionInterface;
use Fxp\Bundle\RequireAssetBundle\Exception\InvalidArgumentException;

/**
 * Factory of assetic file extension config.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class FileExtensionFactory
{
    /**
     * Creates the config of default file extension.
     *
     * @param array $config The config of file extension
     *
     * @return FileExtensionInterface
     *
     * @throws InvalidArgumentException When the "name" key does not exist
     */
    public static function create(array $config = array())
    {
        if (!isset($config['name'])) {
            throw new InvalidArgumentException('The key "name" of file extention config must be present');
        }

        $name = $config['name'];
        $options = array_key_exists('options', $config) ? $config['options'] : array();
        $filters = array_key_exists('filters', $config) ? $config['filters'] : array();
        $debug = array_key_exists('debug', $config) ? $config['debug'] : false;
        $exclude = array_key_exists('exclude', $config) ? $config['exclude'] : false;

        return new FileExtension($name, $options, $filters, $debug, $exclude);
    }

    /**
     * Converts file extension instance to array.
     *
     * @param FileExtensionInterface $extension The file extension
     * @param bool                   $allFields Include or not all the fields
     *
     * @return array The config of file extension
     */
    public static function convertToArray(FileExtensionInterface $extension, $allFields = true)
    {
        $value = array(
            'name' => $extension->getName(),
        );

        if ($allFields || count($extension->getOptions()) > 0) {
            $value['options'] = $extension->getOptions();
        }

        if ($allFields || count($extension->getFilters()) > 0) {
            $value['filters'] = $extension->getFilters();
        }

        if ($allFields || false !== $extension->isDebug()) {
            $value['debug'] = $extension->isDebug();
        }

        if ($allFields || false !== $extension->isExclude()) {
            $value['exclude'] = $extension->isExclude();
        }

        return $value;
    }
}
