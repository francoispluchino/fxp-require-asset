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
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManager;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManager;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PackageInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PackageManager;
use Fxp\Component\RequireAsset\Assetic\Config\PackageManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManager;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Factory\Loader\RequireAssetLoader;
use Fxp\Component\RequireAsset\Assetic\Factory\Resource\CommonRequireAssetResource;
use Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResource;
use Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResourceInterface;
use Fxp\Component\RequireAsset\Assetic\Util\ResourceUtils;
use Fxp\Component\RequireAsset\Assetic\Util\Utils;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Require asset package.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetManager implements RequireAssetManagerInterface
{
    /**
     * @var FileExtensionManagerInterface
     */
    protected $extensionManager;

    /**
     * @var PatternManagerInterface
     */
    protected $patternManager;

    /**
     * @var OutputManagerInterface
     */
    protected $outputManager;

    /**
     * @var RequireLocaleManagerInterface
     */
    protected $localeManager;

    /**
     * @var PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @var CommonRequireAssetResource[]
     */
    protected $commons;

    /**
     * @var RequireAssetCacheInterface
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @param FileExtensionManagerInterface $extensionManager The file extension manager
     * @param PatternManagerInterface       $patternManager   The pattern manager
     * @param OutputManagerInterface        $outputManager    The output manager
     * @param RequireLocaleManagerInterface $localeManager    The locale manager
     */
    public function __construct(FileExtensionManagerInterface $extensionManager = null, PatternManagerInterface $patternManager = null, OutputManagerInterface $outputManager = null, RequireLocaleManagerInterface $localeManager = null)
    {
        $this->initManagers($extensionManager, $patternManager);
        $this->initManagers2($outputManager, $localeManager);
        $this->packageManager = new PackageManager($this->extensionManager, $this->patternManager);
        $this->commons = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getFileExtensionManager()
    {
        return $this->extensionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getPatternManager()
    {
        return $this->patternManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputManager()
    {
        return $this->outputManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleManager()
    {
        return $this->localeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getPackageManager()
    {
        return $this->packageManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setCache(RequireAssetCacheInterface $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCache()
    {
        return $this->cache;
    }

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
     * Init managers.
     *
     * @param FileExtensionManagerInterface $extensionManager
     * @param PatternManagerInterface       $patternManager
     */
    protected function initManagers(FileExtensionManagerInterface $extensionManager = null, PatternManagerInterface $patternManager = null)
    {
        $this->extensionManager = $extensionManager ?: new FileExtensionManager();
        $this->patternManager = $patternManager ?: new PatternManager();
    }

    /**
     * Init managers 2.
     *
     * @param OutputManagerInterface        $outputManager
     * @param RequireLocaleManagerInterface $localeManager
     */
    protected function initManagers2(OutputManagerInterface $outputManager = null, RequireLocaleManagerInterface $localeManager = null)
    {
        $this->outputManager = $outputManager ?: new OutputManager();
        $this->localeManager = $localeManager ?: new RequireLocaleManager();
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
        $locales = $this->findCommonAssetLocales($resource);

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
        $localeInputs = $this->getLocaleCommonInputs($resource, $locale);
        $name = $resource->getPrettyName().'__'.strtolower($locale);
        $targetPath = $this->convertLocaleTartgetPath($resource, $locale);
        $filters = $resource->getFilters();
        $options = $resource->getOptions();

        return new CommonRequireAssetResource($name, $localeInputs, $targetPath, $filters, $options);
    }

    /**
     * Get the locale common inputs.
     *
     * @param RequireAssetResourceInterface $resource The require resource
     * @param string                        $locale   The locale
     *
     * @return string[]
     */
    protected function getLocaleCommonInputs(RequireAssetResourceInterface $resource, $locale)
    {
        $localeInputs = array();

        foreach ($resource->getInputs() as $input) {
            $localeInputs = array_merge($localeInputs, $this->getLocaleManager()->getLocalizedAsset($input, $locale));
        }

        return $localeInputs;
    }

    /**
     * Convert the target path to the locale target path.
     *
     * @param RequireAssetResourceInterface $resource The require resource
     * @param string                        $locale   The locale
     *
     * @return string
     */
    protected function convertLocaleTartgetPath(RequireAssetResourceInterface $resource, $locale)
    {
        $targetPath = $this->convertTargetPath($resource->getTargetPath());
        $pos = strrpos($targetPath, '.');

        if (false !== $pos) {
            $a = substr($targetPath, 0, $pos);
            $b = substr($targetPath, $pos);
            $targetPath = $a.'-'.str_replace('_', '-', strtolower($locale)).$b;
        }

        return $targetPath;
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

    /**
     * Fins the locales of common asset.
     *
     * @param RequireAssetResourceInterface $resource
     *
     * @return string[]
     */
    protected function findCommonAssetLocales(RequireAssetResourceInterface $resource)
    {
        $locales = array();

        foreach ($resource->getInputs() as $input) {
            $locales = array_merge($locales, $this->getLocaleManager()->getAssetLocales($input));
        }

        return array_unique($locales);
    }
}
