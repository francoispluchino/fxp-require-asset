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
 * Interface of config file extension manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface FileExtensionManagerInterface
{
    /**
     * Check if the config of default file extension exist.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasDefaultExtension($name);

    /**
     * Adds the config of default file extension.
     *
     * @param array $config The config of file extension
     *
     * @return self
     *
     * @throws BadMethodCallException   When the manager is resolved
     * @throws InvalidArgumentException When the "name" key does not exist
     */
    public function addDefaultExtension(array $config);

    /**
     * Adds the configs of default file extension.
     *
     * @param array $configs The list of config of the file extension
     *
     * @return self
     *
     * @throws BadMethodCallException When the manager is resolved
     */
    public function addDefaultExtensions(array $configs);

    /**
     * Removes the config of default file extension.
     *
     * @param string $name
     *
     * @return self
     *
     * @throws BadMethodCallException When the manager is resolved
     */
    public function removeDefaultExtension($name);

    /**
     * Gets the config of default file extension.
     *
     * @param string $name
     *
     * @return FileExtensionInterface
     *
     * @throws InvalidConfigurationException When the config of file extension does not exist
     */
    public function getDefaultExtension($name);

    /**
     * Gets the list of config of default file extension.
     *
     * @return array
     */
    public function getDefaultExtensions();
}
