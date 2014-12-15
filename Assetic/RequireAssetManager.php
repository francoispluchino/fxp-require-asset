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
use Fxp\Component\RequireAsset\Assetic\Config\PackageInterface;
use Fxp\Component\RequireAsset\Assetic\Factory\Loader\RequireAssetLoader;
use Fxp\Component\RequireAsset\Assetic\Factory\Resource\CommonRequireAssetResource;
use Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResource;
use Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResourceInterface;
use Fxp\Component\RequireAsset\Assetic\Util\LocaleUtils;
use Fxp\Component\RequireAsset\Assetic\Util\ResourceUtils;
use Fxp\Component\RequireAsset\Assetic\Util\Utils;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Require asset package.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetManager extends AbstractRequireAssetManager
{
    /**
     * {@inheritdoc}
     */
    public function addCommonAsset($name, array $inputs, $targetPath, array $filters = array(), array $options = array())
    {
        $common = new CommonRequireAssetResource($name, $inputs, $this->convertTargetPath($targetPath), $filters, $options);
        $this->commons[Utils::formatName($name)] = $common;

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

        $this->findAssets($assetManager);
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
     * Find the require assets.
     *
     * @param LazyAssetManager $assetManager
     */
    protected function findAssets(LazyAssetManager $assetManager)
    {
        $resources = array();

        foreach ($this->packageManager->getPackages() as $package) {
            $resources = array_merge($resources, $this->addPackageAssets($assetManager, $package));
        }
        $resources = array_merge($resources, $this->loadCommonAssets($assetManager));

        if (null !== $this->cache) {
            $this->cache->setResources($resources);
        }
    }

    /**
     * Adds the assets of packages.
     *
     * @param LazyAssetManager $assetManager The asset manager
     * @param PackageInterface $package      The asset package instance
     *
     * @return RequireAssetResource[]
     */
    protected function addPackageAssets(LazyAssetManager $assetManager, PackageInterface $package)
    {
        $resources = array();

        foreach ($package->getFiles($assetManager->isDebug()) as $file) {
            $resource = $this->createAssetResource($package, $file);
            $resources[] = $resource;
            $assetManager->addResource($resource, 'fxp_require_asset_loader');
        }

        return $resources;
    }

    /**
     * Creates the asset resource.
     *
     * @param PackageInterface $package The asset package instance
     * @param SplFileInfo      $file    The Spo file info instance
     *
     * @return RequireAssetResource
     */
    protected function createAssetResource(PackageInterface $package, SplFileInfo $file)
    {
        $c = ResourceUtils::createConfigResource($package, $file, $this->outputManager);

        return new RequireAssetResource($c[0], $c[1], $c[2], $c[3], $c[4]);
    }

    /**
     * Load the common assets in the asset manager.
     *
     * @param LazyAssetManager $assetManager
     *
     * @return RequireAssetResourceInterface[]
     */
    protected function loadCommonAssets(LazyAssetManager $assetManager)
    {
        $resources = array();

        foreach ($this->commons as $resource) {
            $assetManager->addResource($resource, 'fxp_require_asset_loader');
            $resources = array_merge(
                $resources,
                array($resource),
                $this->loadLocalizedCommonAssets($assetManager, $resource)
            );
        }

        return $resources;
    }

    /**
     * Load the localized common assets in the asset manager.
     *
     * @param LazyAssetManager              $assetManager
     * @param RequireAssetResourceInterface $resource
     *
     * @return RequireAssetResourceInterface[]
     */
    protected function loadLocalizedCommonAssets(LazyAssetManager $assetManager, RequireAssetResourceInterface $resource)
    {
        $resources = array();
        $locales = LocaleUtils::findCommonAssetLocales($resource->getInputs(), $this->getLocaleManager());

        foreach ($locales as $locale) {
            $localeResource = $this->createLocaleAssetResource($resource, $locale);
            $assetManager->addResource($localeResource, 'fxp_require_asset_loader');
            $resources[] = $localeResource;
            $this->getLocaleManager()->addLocaliszedAsset($resource->getPrettyName(), $locale, $localeResource->getPrettyName());
        }

        return $resources;
    }

    /**
     * Create the locale common asset.
     *
     * @param RequireAssetResourceInterface $resource The require resource
     * @param string                        $locale   The locale
     *
     * @return CommonRequireAssetResource
     */
    protected function createLocaleAssetResource(RequireAssetResourceInterface $resource, $locale)
    {
        $localeInputs = LocaleUtils::getLocaleCommonInputs($resource->getInputs(), $locale, $this->getLocaleManager());
        $name = LocaleUtils::formatLocaleCommonName($resource->getPrettyName(), $locale);
        $targetPath = LocaleUtils::convertLocaleTartgetPath($resource->getTargetPath(), $locale);
        $targetPath = $this->convertTargetPath($targetPath);
        $filters = $resource->getFilters();
        $options = $resource->getOptions();

        return new CommonRequireAssetResource($name, $localeInputs, $targetPath, $filters, $options);
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
        return $this->outputManager->convertOutput(trim($targetPath, '/'));
    }
}
