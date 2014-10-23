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
use Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResource;
use Fxp\Component\RequireAsset\Assetic\Util\ResourceUtils;
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
     * @var PackageManagerInterface
     */
    protected $packageManager;

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
     */
    public function __construct(FileExtensionManagerInterface $extensionManager = null, PatternManagerInterface $patternManager = null, OutputManagerInterface $outputManager = null)
    {
        $this->extensionManager = $extensionManager ?: new FileExtensionManager();
        $this->patternManager = $patternManager ?: new PatternManager();
        $this->outputManager = $outputManager ?: new OutputManager();
        $this->packageManager = new PackageManager($this->extensionManager, $this->patternManager);
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
                $assetManager->addResource($resource, 'fxp_require_asset_loader');
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
}
