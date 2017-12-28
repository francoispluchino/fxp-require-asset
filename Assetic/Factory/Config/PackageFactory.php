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
    public static function create(array $config = [], array $defaultExts = [], array $defaultPatterns = [])
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
    public static function createConfig(array $config = [], array $defaultExts = [], array $defaultPatterns = [])
    {
        if (!isset($config['name'])) {
            throw new InvalidArgumentException('The key "name" of package config must be present');
        }

        $sourcePath = self::getConfigValue($config, 'source_path');
        $sourceBase = self::getConfigValue($config, 'source_base');
        $configPackage = new ConfigPackage($config['name'], $sourcePath, $sourceBase);
        $configPackage->setReplaceDefaultExtensions(self::fieldIsTrue('replace_default_extensions', $config));
        $configPackage->setReplaceDefaultPatterns(self::fieldIsTrue('replace_default_patterns', $config));

        self::addDefaultConfig($configPackage, 'replaceDefaultExtensions', 'addExtension', $defaultExts);
        self::addDefaultConfig($configPackage, 'replaceDefaultPatterns', 'addPattern', $defaultPatterns);
        self::addExtensionConfig($config, $configPackage);
        self::addPatternConfig($config, $configPackage);

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
    public static function merge(array $packages, array $defaultExts = [], array $defaultPatterns = [])
    {
        $nodeConfig = PackageConfiguration::getNode();
        $configs = [];

        foreach ($packages as $package) {
            $configs[] = [$package->getName() => static::convertToArray($package)];
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
        $value = [
            'name' => $package->getName(),
        ];

        Utils::addStringField($value, 'source_path', $package, 'getSourcePath', $allFields);
        Utils::addStringField($value, 'source_base', $package, 'getSourceBase', $allFields);
        Utils::addBoolField($value, 'replace_default_extensions', $package, 'replaceDefaultExtensions', true, $allFields);
        Utils::addBoolField($value, 'replace_default_patterns', $package, 'replaceDefaultPatterns', true, $allFields);

        if ($allFields || count($package->getExtensions()) > 0) {
            $value['extensions'] = [];

            foreach ($package->getExtensions() as $extension) {
                $extConfig = FileExtensionFactory::convertToArray($extension);
                $value['extensions'][$extension->getName()] = $extConfig;
            }
        }

        Utils::addArrayField($value, 'patterns', $package, 'getPatterns', 0, $allFields);

        return $value;
    }

    /**
     * Format the extension config.
     *
     * @param string       $extName The extension name
     * @param array|string $confExt The extension config or the extension name
     *
     * @return array The extension config
     */
    protected static function formatExtensionConfig($extName, $confExt)
    {
        if (is_string($confExt)) {
            $confExt = ['name' => $confExt];
        } elseif (!array_key_exists('name', $confExt)) {
            $confExt['name'] = $extName;
        }

        return $confExt;
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

    /**
     * Get the config value.
     *
     * @param array  $config The config
     * @param string $field  The field
     *
     * @return string|null
     */
    protected static function getConfigValue(array $config, $field)
    {
        return isset($config[$field]) ? $config[$field] : null;
    }

    /**
     * Add the default config.
     *
     * @param ConfigPackageInterface $package   The config package
     * @param string                 $method    The check method
     * @param string                 $addMethod The method name for add default config
     * @param array                  $defaults  The list default value of config
     */
    protected static function addDefaultConfig(ConfigPackageInterface $package, $method, $addMethod, array $defaults = [])
    {
        if (!$package->$method()) {
            foreach ($defaults as $default) {
                $package->$addMethod($default);
            }
        }
    }

    /**
     * Add the file extension config.
     *
     * @param array                  $config  The config
     * @param ConfigPackageInterface $package The package instance
     */
    protected static function addExtensionConfig(array $config, ConfigPackageInterface $package)
    {
        if (array_key_exists('extensions', $config)) {
            foreach ($config['extensions'] as $extName => $confExt) {
                $confExt = static::formatExtensionConfig($extName, $confExt);
                $confExt = FileExtensionFactory::create($confExt);
                $package->addExtension($confExt);
            }
        }
    }

    /**
     * Add the pattern config.
     *
     * @param array                  $config  The config
     * @param ConfigPackageInterface $package The package instance
     */
    protected static function addPatternConfig(array $config, ConfigPackageInterface $package)
    {
        if (array_key_exists('patterns', $config)) {
            foreach ($config['patterns'] as $pattern) {
                $package->addPattern($pattern);
            }
        }
    }
}
