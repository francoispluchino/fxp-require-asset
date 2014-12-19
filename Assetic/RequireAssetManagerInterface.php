<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic;

use Assetic\Factory\LazyAssetManager;
use Fxp\Component\RequireAsset\Assetic\Cache\RequireAssetCacheInterface;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\LocaleManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PackageManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManagerInterface;

/**
 * Interface of require asset manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface RequireAssetManagerInterface
{
    /**
     * Get the file extension manager.
     *
     * @return FileExtensionManagerInterface
     */
    public function getFileExtensionManager();

    /**
     * Get the pattern manager.
     *
     * @return PatternManagerInterface
     */
    public function getPatternManager();

    /**
     * Get the output rewrite manager.
     *
     * @return OutputManagerInterface
     */
    public function getOutputManager();

    /**
     * Get the locale manager.
     *
     * @return LocaleManagerInterface
     */
    public function getLocaleManager();

    /**
     * Get the package manager.
     *
     * @return PackageManagerInterface
     */
    public function getPackageManager();

    /**
     * Set the require asset cache.
     *
     * @param RequireAssetCacheInterface $cache
     *
     * @return self
     */
    public function setCache(RequireAssetCacheInterface $cache);

    /**
     * Get the require asset cache.
     *
     * @return RequireAssetCacheInterface
     */
    public function getCache();

    /**
     * Add the common require asset.
     *
     * @param string $name       The forumlae name
     * @param array  $inputs     The require assets source path
     * @param string $targetPath The formulae target path
     * @param array  $filters    The formulae filters
     * @param array  $options    The formulae filters
     *
     * @return self
     */
    public function addCommonAsset($name, array $inputs, $targetPath, array $filters = array(), array $options = array());

    /**
     * Add all require asset resources in assetic lazy asset manager.
     *
     * @param LazyAssetManager $assetManager The assetic lazy asset manager
     *
     * @return void
     */
    public function addAssetResources(LazyAssetManager $assetManager);
}
