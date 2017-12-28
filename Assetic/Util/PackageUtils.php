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

use Fxp\Component\RequireAsset\Assetic\Config\ConfigPackageInterface;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PackageManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Factory\Config\PackageFactory;

/**
 * Package Utils.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class PackageUtils
{
    /**
     * Create the config of asset package.
     *
     * @param string|array|ConfigPackageInterface $name                   The name of package or config or instance
     * @param string|null                         $sourcePath             The package source path
     * @param FileExtensionInterface[]|array      $extensions             The file extensions
     * @param string[]                            $patterns               The patterns
     * @param bool                                $replaceDefaultExts     Replace the default file extensions or add new file extensions
     * @param bool                                $replaceDefaultPatterns Replace the default patterns or add new patterns
     * @param string|null                         $sourceBase             The package source base
     *
     * @return ConfigPackageInterface
     */
    public static function createByConfig($name, $sourcePath = null, array $extensions = [], array $patterns = [], $replaceDefaultExts = false, $replaceDefaultPatterns = false, $sourceBase = null)
    {
        if (!$name instanceof ConfigPackageInterface) {
            $config = is_array($name) ? $name
                : [
                    'name' => $name,
                    'source_path' => $sourcePath,
                    'extensions' => $extensions,
                    'patterns' => $patterns,
                    'replace_default_extensions' => $replaceDefaultExts,
                    'replace_default_patterns' => $replaceDefaultPatterns,
                    'source_base' => $sourceBase,
                ]
            ;

            $name = PackageFactory::createConfig($config);
        }

        return $name;
    }

    /**
     * Get the paths of asset packages.
     *
     * @param PackageManagerInterface $manager The package manager
     *
     * @return array The map of package name and source path
     */
    public static function getPackagePaths(PackageManagerInterface $manager)
    {
        $packages = [];

        foreach ($manager->getPackages() as $package) {
            $packages[$package->getName()] = $package->getSourcePath();
        }

        return $packages;
    }
}
