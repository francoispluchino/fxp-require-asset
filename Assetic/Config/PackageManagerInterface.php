<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic\Config;

use Fxp\Component\RequireAsset\Exception\BadMethodCallException;
use Fxp\Component\RequireAsset\Exception\InvalidArgumentException;
use Fxp\Component\RequireAsset\Exception\InvalidConfigurationException;

/**
 * Interface of config asset package manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface PackageManagerInterface
{
    /**
     * Check if the config of asset package exist.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasPackage($name);

    /**
     * Adds the config of asset package.
     *
     * @param string|array|ConfigPackageInterface $name                   The name of package or config or instance
     * @param string|null                         $sourcePath             The package source path
     * @param FileExtensionInterface[]|array      $extensions             The file extensions
     * @param string[]                            $patterns               The patterns
     * @param bool                                $replaceDefaultExts     Replace the default file extensions or add new file extensions
     * @param bool                                $replaceDefaultPatterns Replace the default patterns or add new patterns
     * @param string|null                         $sourceBase             The package source base
     *
     * @return self
     *
     * @throws BadMethodCallException   When the manager is resolved
     * @throws InvalidArgumentException When the "name" argument is a string and the "sourcePath" argument is empty
     */
    public function addPackage($name, $sourcePath = null, array $extensions = [], array $patterns = [], $replaceDefaultExts = false, $replaceDefaultPatterns = false, $sourceBase = null);

    /**
     * Adds the configs of package.
     *
     * @param array $configs The list of config of package (config array or file config package instance)
     *
     * @return self
     *
     * @throws BadMethodCallException When the manager is resolved
     */
    public function addPackages(array $configs);

    /**
     * Removes the config of asset package.
     *
     * @param string $name
     *
     * @return self
     *
     * @throws BadMethodCallException When the manager is resolved
     */
    public function removePackage($name);

    /**
     * Gets the config of asset package, with include default extensions and default patterns.
     *
     * @param string $name
     *
     * @return PackageInterface
     *
     * @throws InvalidConfigurationException When the config of package does not exist
     */
    public function getPackage($name);

    /**
     * Gets the all config of asset packages.
     *
     * @return PackageInterface[]
     */
    public function getPackages();
}
