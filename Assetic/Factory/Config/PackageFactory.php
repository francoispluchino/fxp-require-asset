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

use Fxp\Bundle\RequireAssetBundle\Assetic\Config\ConfigPackage;
use Fxp\Bundle\RequireAssetBundle\Assetic\Config\FileExtensionInterface;
use Fxp\Bundle\RequireAssetBundle\Assetic\Config\PackageInterface;
use Fxp\Bundle\RequireAssetBundle\Exception\InvalidArgumentException;

/**
 * Factory of assetic package config.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class PackageFactory
{
    /**
     * Creates the config of asset package.
     *
     * @param array                    $config          The config of package
     * @param FileExtensionInterface[] $defaultExts     The list of default file extensions
     * @param string[]                 $defaultPatterns The list of default patterns
     *
     * @return PackageInterface
     *
     * @throws InvalidArgumentException When the "name" key does not exist
     */
    public static function create(array $config = array(), array $defaultExts = array(), array $defaultPatterns = array())
    {
        if (!isset($config['name'])) {
            throw new InvalidArgumentException('The key "name" of package config must be present');
        }

        if (!isset($config['source_path'])) {
            throw new InvalidArgumentException(sprintf('The key "source_path" of package "%s" config must be present', $config['name']));
        }

        $sourceBase = isset($config['source_base']) ? $config['source_base'] : null;
        $configPackage = new ConfigPackage($config['name'], $config['source_path'], $sourceBase);

        if (!self::fieldIsTrue('replace_default_extensions', $config)) {
            foreach ($defaultExts as $extension) {
                $extension = FileExtensionFactory::convertToArray($extension, false);
                $configPackage->addExtension($extension);
            }
        }

        if (array_key_exists('extensions', $config)) {
            foreach ($config['extensions'] as $extName => $confExt) {
                $confExt['name'] = $extName;
                $configPackage->addExtension($confExt);
            }
        }

        if (!self::fieldIsTrue('replace_default_patterns', $config)) {
            foreach ($defaultPatterns as $pattern) {
                $configPackage->addPattern($pattern);
            }
        }

        if (array_key_exists('patterns', $config)) {
            foreach ($config['patterns'] as $pattern) {
                $configPackage->addPattern($pattern);
            }
        }

        return $configPackage->getPackage();
    }

    /**
     * Checks if the field is bool and if is TRUE.
     *
     * @param string $field  The field name
     * @param array  $config The config
     *
     * @return bool
     */
    protected static function fieldIsTrue($field, array $config)
    {
        if (array_key_exists($field, $config) && true === $config[$field]) {
            return true;
        }

        return false;
    }
}
