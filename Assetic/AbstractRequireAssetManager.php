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

use Fxp\Component\RequireAsset\Assetic\Cache\RequireAssetCacheInterface;
use Fxp\Component\RequireAsset\Assetic\Config\AssetResourceInterface;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManager;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\LocaleManager;
use Fxp\Component\RequireAsset\Assetic\Config\LocaleManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManager;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PackageManager;
use Fxp\Component\RequireAsset\Assetic\Config\PackageManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManager;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManagerInterface;

/**
 * Abstract class for Require asset package.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractRequireAssetManager implements RequireAssetManagerInterface
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
     * @var AssetResourceInterface[]
     */
    protected $commons;

    /**
     * @var RequireAssetCacheInterface
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
        $this->commons = array();
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
     * Initialize the manager.
     */
    protected function init()
    {
        if (!$this->initialized) {
            $this->extensionManager = $this->extensionManager ?: new FileExtensionManager();
            $this->patternManager = $this->patternManager ?: new PatternManager();
            $this->outputManager = $this->outputManager ?: new OutputManager();
            $this->localeManager = $this->localeManager ?: new LocaleManager();
            $this->packageManager = $this->packageManager ?: new PackageManager($this->extensionManager, $this->patternManager);
            $this->initialized = true;
        }
    }
}
