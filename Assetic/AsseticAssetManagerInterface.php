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
use Fxp\Component\RequireAsset\Asset\Config\AssetReplacementManagerInterface;
use Fxp\Component\RequireAsset\Asset\Config\LocaleManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Cache\AsseticAssetCacheInterface;
use Fxp\Component\RequireAsset\Assetic\Config\AsseticConfigResourcesInterface;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PackageManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManagerInterface;

/**
 * Interface of assetic asset manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface AsseticAssetManagerInterface
{
    /**
     * Set the file extension manager.
     *
     * @param FileExtensionManagerInterface $extensionManager The file extension manager
     *
     * @return self
     */
    public function setFileExtensionManager(FileExtensionManagerInterface $extensionManager);

    /**
     * Get the file extension manager.
     *
     * @return FileExtensionManagerInterface
     */
    public function getFileExtensionManager();

    /**
     * set the pattern manager.
     *
     * @param PatternManagerInterface $patternManager The pattern manager
     *
     * @return self
     */
    public function setPatternManager(PatternManagerInterface $patternManager);

    /**
     * Get the pattern manager.
     *
     * @return PatternManagerInterface
     */
    public function getPatternManager();

    /**
     * Set the output rewrite manager.
     *
     * @param OutputManagerInterface $outputManager The output rewrite manager
     *
     * @return self
     */
    public function setOutputManager(OutputManagerInterface $outputManager);

    /**
     * Get the output rewrite manager.
     *
     * @return OutputManagerInterface
     */
    public function getOutputManager();

    /**
     * Set the locale manager.
     *
     * @param LocaleManagerInterface $localeManager The locale manager
     *
     * @return self
     */
    public function setLocaleManager(LocaleManagerInterface $localeManager);

    /**
     * Get the locale manager.
     *
     * @return LocaleManagerInterface
     */
    public function getLocaleManager();

    /**
     * Set the package manager.
     *
     * @param PackageManagerInterface $packageManager The package manager
     *
     * @return self
     */
    public function setPackageManager(PackageManagerInterface $packageManager);

    /**
     * Get the package manager.
     *
     * @return PackageManagerInterface
     */
    public function getPackageManager();

    /**
     * Set the asset replacement manager.
     *
     * @param AssetReplacementManagerInterface $replacementManager The asset replacement manager
     *
     * @return self
     */
    public function setAssetReplacementManager(AssetReplacementManagerInterface $replacementManager);

    /**
     * Get the asset replacement manager.
     *
     * @return AssetReplacementManagerInterface
     */
    public function getAssetReplacementManager();

    /**
     * Set the assetic asset cache.
     *
     * @param AsseticAssetCacheInterface $cache
     *
     * @return self
     */
    public function setCache(AsseticAssetCacheInterface $cache);

    /**
     * Get the assetic asset cache.
     *
     * @return AsseticAssetCacheInterface
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
    public function addCommonAsset($name, array $inputs, $targetPath, array $filters = [], array $options = []);

    /**
     * Add all require asset resources in assetic lazy asset manager.
     *
     * @param LazyAssetManager $assetManager The assetic lazy asset manager
     */
    public function addAssetResources(LazyAssetManager $assetManager);

    /**
     * Get the configs of the require assets.
     *
     * @param bool $debug The assetic debug mode
     *
     * @return AsseticConfigResourcesInterface
     */
    public function getAsseticConfigResources($debug);
}
