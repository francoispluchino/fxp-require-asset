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
     * Adds the file extension.
     *
     * @param string|array|FileExtensionInterface $name      The name of extension or config or instance
     * @param array                               $options   The assetic formulae options
     * @param array                               $filters   The assetic formulae filters
     * @param string|null                         $extension The output extension
     * @param bool                                $debug     The debug mode
     * @param bool                                $exclude   Exclude or not the file extension
     *
     * @return self
     */
    public function addExtension($name, array $options = [], array $filters = [], $extension = null, $debug = false, $exclude = false);

    /**
     * Removes the config of extension.
     *
     * @param string $name
     *
     * @return self
     */
    public function removeExtension($name);

    /**
     * Set replace the default file extensions by the file extensions in package.
     *
     * @param bool $replace
     *
     * @return self
     */
    public function setReplaceDefaultExtensions($replace);

    /**
     * Replace the default file extensions by the file extensions in package.
     *
     * @return bool
     */
    public function replaceDefaultExtensions();

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
     * Set replace the default patterns by the patterns in package.
     *
     * @param bool $replace
     *
     * @return self
     */
    public function setReplaceDefaultPatterns($replace);

    /**
     * Replace the default patterns by the patterns in package.
     *
     * @return bool
     */
    public function replaceDefaultPatterns();

    /**
     * Gets the package instance.
     *
     * @return PackageInterface
     */
    public function getPackage();
}
