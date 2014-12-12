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
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManager;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManager;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PackageManager;
use Fxp\Component\RequireAsset\Assetic\Config\PackageManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManager;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Factory\Resource\CommonRequireAssetResource;

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
}
