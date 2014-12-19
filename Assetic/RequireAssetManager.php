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
use Fxp\Component\RequireAsset\Assetic\Config\AsseticConfigResources;
use Fxp\Component\RequireAsset\Assetic\Config\AsseticConfigResourcesInterface;
use Fxp\Component\RequireAsset\Assetic\Config\AssetResource;
use Fxp\Component\RequireAsset\Assetic\Config\AssetResourceInterface;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PackageInterface;
use Fxp\Component\RequireAsset\Assetic\Factory\Loader\RequireAssetLoader;
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
        $classname = 'Fxp\Component\RequireAsset\Assetic\Factory\Resource\CommonRequireAssetResource';
        $args = array($name, $inputs, $this->convertTargetPath($targetPath), $filters, $options);
        $resource = $this->createAssetResource($name, $classname, $args);

        $this->commons[Utils::formatName($name)] = $resource;

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
        $asseticResources = array();

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
            $resource = $this->createAssetResourceByPackage($package, $file, $this->getOutputManager());
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
                $this->getLocaleManager()->addLocalizedAsset($name, $locale, array($resource->getPrettyName()));
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
                $localeResource = $this->createLocaleAssetResource($instance, $locale);
                $configs->addResource($localeResource);
                $this->getLocaleManager()->addLocalizedAsset($instance->getPrettyName(), $locale, $localeResource->getPrettyName());
            }
        }
    }

    /**
     * Create the locale common asset resource.
     *
     * @param RequireAssetResourceInterface $resource The require resource
     * @param string                        $locale   The locale
     *
     * @return AssetResourceInterface
     */
    protected function createLocaleAssetResource(RequireAssetResourceInterface $resource, $locale)
    {
        $localeInputs = LocaleUtils::getLocaleCommonInputs($resource->getInputs(), $locale, $this->getLocaleManager());
        $name = LocaleUtils::formatLocaleCommonName($resource->getPrettyName(), $locale);
        $targetPath = LocaleUtils::convertLocaleTartgetPath($resource->getTargetPath(), $locale);
        $classname = 'Fxp\Component\RequireAsset\Assetic\Factory\Resource\CommonRequireAssetResource';
        $args = array($name, $localeInputs, $targetPath, $resource->getFilters(), $resource->getOptions());

        return $this->createAssetResource($name, $classname, $args);
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

    /**
     * Creates the asset resource.
     *
     * @param PackageInterface       $package       The asset package instance
     * @param SplFileInfo            $file          The Spo file info instance
     * @param OutputManagerInterface $outputManager The output manager
     *
     * @return AssetResourceInterface
     */
    protected function createAssetResourceByPackage(PackageInterface $package, SplFileInfo $file, OutputManagerInterface $outputManager)
    {
        $classname = 'Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResource';
        $args = ResourceUtils::createConfigResource($package, $file, $outputManager);

        return $this->createAssetResource($args[0], $classname, $args);
    }

    /**
     * Create the config asset resource.
     *
     * @param string $name      The require asset name
     * @param string $classname The classname
     * @param array  $arguments The arguments
     *
     * @return AssetResource
     */
    protected function createAssetResource($name, $classname, array $arguments)
    {
        return new AssetResource($name, $classname, 'fxp_require_asset_loader', $arguments);
    }
}
