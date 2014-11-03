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

use Fxp\Component\RequireAsset\Exception\InvalidConfigurationException;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Interface of compiled config asset package.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface PackageInterface extends ConfigInterface
{
    /**
     * Gets the source path.
     *
     * @return string|null
     */
    public function getSourcePath();

    /**
     * Gets the source base.
     * The dirname of source path or custom name.
     *
     * @return string|null
     */
    public function getSourceBase();

    /**
     * Check if the config of extension exist.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasExtension($name);

    /**
     * Gets the config of extension.
     *
     * @param string $name
     *
     * @return FileExtensionInterface
     *
     * @throws InvalidConfigurationException When the config of extension does not exist
     */
    public function getExtension($name);

    /**
     * Gets the config of extensions.
     *
     * @return FileExtensionInterface[]
     */
    public function getExtensions();

    /**
     * Check if the config of pattern exist.
     *
     * @param string $pattern
     *
     * @return bool
     */
    public function hasPattern($pattern);

    /**
     * Gets the config of patterns.
     *
     * @return string[]
     */
    public function getPatterns();

    /**
     * Get the files of package.
     *
     * @param bool $debug The debug mode
     *
     * @return SplFileInfo[]
     */
    public function getFiles($debug = false);
}
