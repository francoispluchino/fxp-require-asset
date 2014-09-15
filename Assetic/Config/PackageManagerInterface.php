<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Assetic\Config;

use Fxp\Bundle\RequireAssetBundle\Exception\BadMethodCallException;
use Fxp\Bundle\RequireAssetBundle\Exception\InvalidArgumentException;
use Fxp\Bundle\RequireAssetBundle\Exception\InvalidConfigurationException;

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
     * @param array $config The asset config package instance
     *
     * @return self
     *
     * @throws BadMethodCallException   When the manager is resolved
     * @throws InvalidArgumentException When the "name" key does not exist
     */
    public function addPackage(array $config);

    /**
     * Adds the configs of package.
     *
     * @param array $configs The list of config of package
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
