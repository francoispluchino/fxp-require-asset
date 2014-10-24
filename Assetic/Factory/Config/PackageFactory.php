<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic\Factory\Config;

use Fxp\Component\RequireAsset\Assetic\Config\ConfigPackage;
use Fxp\Component\RequireAsset\Assetic\Config\ConfigPackageInterface;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PackageInterface;
use Fxp\Component\RequireAsset\Assetic\Util\Utils;
use Fxp\Component\RequireAsset\Config\PackageConfiguration;
use Fxp\Component\RequireAsset\Exception\InvalidArgumentException;

/**
 * Factory of assetic package config.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class PackageFactory
{
    /**
     * Creates the asset package.
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
        $configPackage = static::createConfig($config, $defaultExts, $defaultPatterns);

        return $configPackage->getPackage();
    }

    /**
     * Creates the config of asset package.
     *
     * @param array                    $config          The config of package
     * @param FileExtensionInterface[] $defaultExts     The list of default file extensions
     * @param string[]                 $defaultPatterns The list of default patterns
     *
     * @return ConfigPackageInterface
     *
     * @throws InvalidArgumentException When the "name" key does not exist
     */
    public static function createConfig(array $config = array(), array $defaultExts = array(), array $defaultPatterns = array())
    {
        if (!isset($config['name'])) {
            throw new InvalidArgumentException('The key "name" of package config must be present');
        }

        $sourcePath = isset($config['source_path']) ? $config['source_path'] : null;
        $sourceBase = isset($config['source_base']) ? $config['source_base'] : null;
        $configPackage = new ConfigPackage($config['name'], $sourcePath, $sourceBase);
        $configPackage->setReplaceDefaultExtensions(self::fieldIsTrue('replace_default_extensions', $config));
        $configPackage->setReplaceDefaultPatterns(self::fieldIsTrue('replace_default_patterns', $config));

        if (!$configPackage->replaceDefaultExtensions()) {
            foreach ($defaultExts as $extension) {
                $configPackage->addExtension($extension);
            }
        }

        if (array_key_exists('extensions', $config)) {
            foreach ($config['extensions'] as $extName => $confExt) {
                $confExt['name'] = $extName;
                $confExt = FileExtensionFactory::create($confExt);
                $configPackage->addExtension($confExt);
            }
        }

        if (!$configPackage->replaceDefaultPatterns()) {
            foreach ($defaultPatterns as $pattern) {
                $configPackage->addPattern($pattern);
            }
        }

        if (array_key_exists('patterns', $config)) {
            foreach ($config['patterns'] as $pattern) {
                $configPackage->addPattern($pattern);
            }
        }

        return $configPackage;
    }

    /**
     * Merge the multiple configuration of a same config package.
     *
     * @param ConfigPackageInterface[] $packages
     * @param FileExtensionInterface[] $defaultExts     The list of default file extensions
     * @param string[]                 $defaultPatterns The list of default patterns
     *
     * @return ConfigPackageInterface The new instance with merged config
     */
    public static function merge(array $packages, array $defaultExts = array(), array $defaultPatterns = array())
    {
        $nodeConfig = PackageConfiguration::getNode();
        $configs = array();

        foreach ($packages as $package) {
            $configs[] = array($package->getName() => static::convertToArray($package));
        }

        $config = Utils::mergeConfigs($nodeConfig, $configs);

        return static::createConfig($config, $defaultExts, $defaultPatterns);
    }

    /**
     * Converts config package instance to array.
     *
     * @param ConfigPackageInterface $package   The config package
     * @param bool                   $allFields Include or not all the fields
     *
     * @return array The config of file extension
     */
    public static function convertToArray(ConfigPackageInterface $package, $allFields = false)
    {
        $value = array(
            'name' => $package->getName(),
        );

        if ($allFields || null !== $package->getSourcePath()) {
            $value['source_path'] = $package->getSourcePath();
        }

        if ($allFields || null !== $package->getSourceBase()) {
            $value['source_base'] = $package->getSourceBase();
        }

        if ($allFields || false !== $package->replaceDefaultExtensions()) {
            $value['replace_default_extensions'] = $package->replaceDefaultExtensions();
        }

        if ($allFields || false !== $package->replaceDefaultPatterns()) {
            $value['replace_default_patterns'] = $package->replaceDefaultPatterns();
        }

        if ($allFields || count($package->getExtensions()) > 0) {
            $value['extensions'] = array();

            foreach ($package->getExtensions() as $extension) {
                $extConfig = FileExtensionFactory::convertToArray($extension);
                $value['extensions'][$extension->getName()] = $extConfig;
            }
        }

        if ($allFields || count($package->getPatterns()) > 0) {
            $value['patterns'] = $package->getPatterns();
        }

        return $value;
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
