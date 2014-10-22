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

/**
 * Interface of config asset package.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface ConfigPackageInterface extends PackageInterface
{
    /**
     * Adds the config of extension by instance.
     *
     * @param FileExtensionInterface $extension The file extension instance
     *
     * @return self
     */
    public function addExtension(FileExtensionInterface $extension);

    /**
     * Adds the config of extension by array.
     *
     * @param string      $name
     * @param array       $options
     * @param array       $filters
     * @param string|null $extension
     * @param bool        $debug
     * @param bool        $exclude
     *
     * @return self
     */
    public function addConfigExtension($name, array $options = array(), array $filters = array(), $extension = null, $debug = false, $exclude = false);

    /**
     * Removes the config of extension.
     *
     * @param string $name
     *
     * @return self
     */
    public function removeExtension($name);

    /**
     * Adds the config of pattern.
     *
     * @param string $pattern The pattern
     *
     * @return self
     */
    public function addPattern($pattern);

    /**
     * Removes the config of pattern.
     *
     * @param string $pattern
     *
     * @return self
     */
    public function removePattern($pattern);

    /**
     * Gets the package instance.
     *
     * @return PackageInterface
     */
    public function getPackage();
}
