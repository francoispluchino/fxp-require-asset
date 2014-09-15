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

/**
 * Interface of config asset package.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface ConfigPackageInterface extends PackageInterface
{
    /**
     * Adds the config of extension.
     *
     * @param array $config The config of file extension
     *
     * @return self
     */
    public function addExtension(array $config);

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
