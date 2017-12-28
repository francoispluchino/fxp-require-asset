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
use Fxp\Component\RequireAsset\Asset\Util\AssetUtils;
use Fxp\Component\RequireAsset\Asset\Util\LocaleUtils;
use Fxp\Component\RequireAsset\Assetic\Config\AsseticConfigResources;
use Fxp\Component\RequireAsset\Assetic\Config\AsseticConfigResourcesInterface;
use Fxp\Component\RequireAsset\Assetic\Config\AssetResourceInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PackageInterface;
use Fxp\Component\RequireAsset\Assetic\Factory\Loader\RequireAssetLoader;
use Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResourceInterface;
use Fxp\Component\RequireAsset\Assetic\Util\AssetResourceUtils;

/**
 * Assetic asset package.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AsseticAssetManager extends AbstractAsseticAssetManager
{
    /**
     * {@inheritdoc}
     */
    public function addCommonAsset($name, array $inputs, $targetPath, array $filters = [], array $options = [])
    {
        $classname = 'Fxp\Component\RequireAsset\Assetic\Factory\Resource\CommonRequireAssetResource';
        $args = [$name, $inputs, $this->convertTargetPath($targetPath), $filters, $options];
        $resource = AssetResourceUtils::createAssetResource($name, $classname, $args, 0);

        $this->commons[AssetUtils::formatName($name)] = $resource;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addAssetResources(LazyAssetManager $assetManager)
    {
        $assetManager->setLoader('fxp_require_asset_loader', new RequireAssetLoader());

        if ($this->loadAssetsInCache($assetManager)) {
            return;
        }

        $configs = $this->getAsseticConfigResources($assetManager->isDebug());
        $asseticResources = $this->addConfigAsseticResources($configs->getResources(), $assetManager);

        if (null !== $this->cache) {
            $this->cache->setResources($asseticResources);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAsseticConfigResources($debug)
    {
        $configs = new AsseticConfigResources();

        foreach ($this->getPackageManager()->getPackages() as $package) {
            $this->addPackageAssets($configs, $package, $debug);
        }
        $this->addCommonAssets($configs);
        $this->replaceAssets($configs);

        return $configs;
    }

    /**
     * Loads the asset resources since the cache.
     *
     * @param LazyAssetManager $assetManager
     *
     * @return bool
     */
    protected function loadAssetsInCache(LazyAssetManager $assetManager)
    {
        if (null !== $this->cache && $this->cache->hasResources()) {
            foreach ($this->cache->getResources() as $resource) {
                if (!$assetManager->has((string) $resource)) {
                    $assetManager->addResource($resource, 'fxp_require_asset_loader');
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Add the assetic asset resources in assetic asset manager.
     *
     * @param AssetResourceInterface[] $configResources The config asset resources
     * @param LazyAssetManager         $assetManager    The assetic asset manager
     *
     * @return RequireAssetResourceInterface[]
     */
    protected function addConfigAsseticResources(array $configResources, LazyAssetManager $assetManager)
    {
        $asseticResources = [];

        foreach ($configResources as $configResource) {
            $resource = $configResource->getNewInstance();
            $assetManager->addResource($resource, $configResource->getLoader());
            $asseticResources[] = $resource;
        }

        return $asseticResources;
    }

    /**
     * Adds the assets of packages.
     *
     * @param AsseticConfigResourcesInterface $configs The assetic config resources
     * @param PackageInterface                $package The asset package instance
     * @param bool                            $debug   The assetic debug mode
     */
    protected function addPackageAssets(AsseticConfigResourcesInterface $configs, PackageInterface $package, $debug = false)
    {
        foreach ($package->getFiles($debug) as $file) {
            $resource = AssetResourceUtils::createAssetResourceByPackage($package, $file, $this->getOutputManager(), $debug);
            $configs->addResource($resource);
        }
    }

    /**
     * Add the common assets in the asset manager.
     *
     * @param AsseticConfigResourcesInterface $configs The assetic config resources
     */
    protected function addCommonAssets(AsseticConfigResourcesInterface $configs)
    {
        foreach ($this->commons as $resource) {
            $configs->addResource($resource);
            $commonName = $resource->getPrettyName();

            if (!preg_match('/__[A-Za-z]{2}$|__[A-Za-z]{2}_[A-Za-z]{2}$/', $commonName)) {
                $this->addLocaleCommonAssets($configs, $resource);
            } else {
                $name = substr($commonName, 0, strrpos($commonName, '__'));
                $locale = substr($commonName, strrpos($commonName, '__') + 2);
                $this->getLocaleManager()->addLocalizedAsset($name, $locale, [$resource->getPrettyName()]);
            }
        }
    }

    /**
     * Load the localized common assets in the asset manager.
     *
     * @param AsseticConfigResourcesInterface $configs  The assetic config resources
     * @param AssetResourceInterface          $resource The config of asset resource
     */
    protected function addLocaleCommonAssets(AsseticConfigResourcesInterface $configs, AssetResourceInterface $resource)
    {
        $instance = $resource->getNewInstance();
        $locales = LocaleUtils::findCommonAssetLocales($instance->getInputs(), $this->getLocaleManager());

        foreach ($locales as $locale) {
            $localeName = LocaleUtils::formatLocaleCommonName($instance->getPrettyName(), $locale);
            if (!isset($this->commons[$localeName])) {
                $localeResource = AssetResourceUtils::createLocaleAssetResource($instance, $locale, $this->getLocaleManager());
                $configs->addResource($localeResource);
                $this->getLocaleManager()->addLocalizedAsset($instance->getPrettyName(), $locale, $localeResource->getPrettyName());
            }
        }
    }

    /**
     * Replace the config asset resource by a new config asset resource.
     *
     * @param AsseticConfigResourcesInterface $configs The assetic config resources
     */
    protected function replaceAssets(AsseticConfigResourcesInterface $configs)
    {
        foreach ($this->getAssetReplacementManager()->getReplacements() as $asset => $replacement) {
            if ($configs->hasResource($replacement)) {
                $configs->removeResource($asset);
            }
        }
    }

    /**
     * Convert the target path.
     *
     * @param string $targetPath The target path
     *
     * @return string
     */
    protected function convertTargetPath($targetPath)
    {
        return $this->getOutputManager()->convertOutput(trim($targetPath, '/'));
    }
}
