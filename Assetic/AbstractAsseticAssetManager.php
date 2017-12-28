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

use Fxp\Component\RequireAsset\Asset\Config\AssetReplacementManager;
use Fxp\Component\RequireAsset\Asset\Config\AssetReplacementManagerInterface;
use Fxp\Component\RequireAsset\Asset\Config\LocaleManager;
use Fxp\Component\RequireAsset\Asset\Config\LocaleManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Cache\AsseticAssetCacheInterface;
use Fxp\Component\RequireAsset\Assetic\Config\AssetResourceInterface;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManager;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManager;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PackageManager;
use Fxp\Component\RequireAsset\Assetic\Config\PackageManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManager;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManagerInterface;

/**
 * Abstract class for Assetic asset package.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractAsseticAssetManager implements AsseticAssetManagerInterface
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
     * @var LocaleManagerInterface
     */
    protected $localeManager;

    /**
     * @var PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @var AssetReplacementManagerInterface
     */
    protected $replacementManager;

    /**
     * @var AssetResourceInterface[]
     */
    protected $commons;

    /**
     * @var AsseticAssetCacheInterface
     */
    protected $cache;

    /**
     * @var bool
     */
    protected $initialized;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->initialized = false;
        $this->commons = [];
    }

    /**
     * {@inheritdoc}
     */
    public function setFileExtensionManager(FileExtensionManagerInterface $extensionManager)
    {
        $this->extensionManager = $extensionManager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileExtensionManager()
    {
        $this->init();

        return $this->extensionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setPatternManager(PatternManagerInterface $patternManager)
    {
        $this->patternManager = $patternManager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPatternManager()
    {
        $this->init();

        return $this->patternManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setOutputManager(OutputManagerInterface $outputManager)
    {
        $this->outputManager = $outputManager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputManager()
    {
        $this->init();

        return $this->outputManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocaleManager(LocaleManagerInterface $localeManager)
    {
        $this->localeManager = $localeManager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleManager()
    {
        $this->init();

        return $this->localeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setPackageManager(PackageManagerInterface $packageManager)
    {
        $this->packageManager = $packageManager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPackageManager()
    {
        $this->init();

        return $this->packageManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setAssetReplacementManager(AssetReplacementManagerInterface $replacementManager)
    {
        $this->replacementManager = $replacementManager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetReplacementManager()
    {
        $this->init();

        return $this->replacementManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setCache(AsseticAssetCacheInterface $cache)
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
     * Initialize the managers.
     */
    protected function init()
    {
        if (!$this->initialized) {
            $this->initManagers1();
            $this->initManagers2();
            $this->initManagers3();
            $this->initialized = true;
        }
    }

    /**
     * Initialize the managers step 1.
     */
    private function initManagers1()
    {
        $this->extensionManager = $this->extensionManager ?: new FileExtensionManager();
        $this->patternManager = $this->patternManager ?: new PatternManager();
    }

    /**
     * Initialize the managers step 2.
     */
    private function initManagers2()
    {
        $this->outputManager = $this->outputManager ?: new OutputManager();
        $this->localeManager = $this->localeManager ?: new LocaleManager();
    }

    /**
     * Initialize the managers step 3.
     */
    private function initManagers3()
    {
        $this->replacementManager = $this->replacementManager ?: new AssetReplacementManager();
        $this->packageManager = $this->packageManager ?: new PackageManager($this->extensionManager, $this->patternManager);
    }
}
