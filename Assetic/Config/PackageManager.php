<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic\Config;

use Fxp\Component\RequireAsset\Assetic\Factory\Config\PackageFactory;
use Fxp\Component\RequireAsset\Exception\BadMethodCallException;
use Fxp\Component\RequireAsset\Exception\InvalidConfigurationException;

/**
 * Config package manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class PackageManager extends AbstractConfigManager implements PackageManagerInterface
{
    /**
     * @var FileExtensionManagerInterface
     */
    protected $extManager;

    /**
     * @var PatternManagerInterface
     */
    protected $patternManager;

    /**
     * @var array
     */
    protected $configPackages;

    /**
     * @var array
     */
    protected $packages;

    /**
     * @var bool
     */
    protected $locked;

    /**
     * Constructor.
     *
     * @param FileExtensionManagerInterface $extManager
     * @param PatternManagerInterface       $patternManager
     */
    public function __construct(FileExtensionManagerInterface $extManager, PatternManagerInterface $patternManager)
    {
        $this->extManager = $extManager;
        $this->patternManager = $patternManager;
        $this->configPackages = [];
        $this->packages = [];
        $this->locked = false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPackage($name)
    {
        return isset($this->packages[$name]) || isset($this->configPackages[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function addPackage($name, $sourcePath = null, array $extensions = [], array $patterns = [], $replaceDefaultExts = false, $replaceDefaultPatterns = false, $sourceBase = null)
    {
        return $this->doAdd('Fxp\Component\RequireAsset\Assetic\Util\PackageUtils', 'configPackages', [$name, $sourcePath, $extensions, $patterns, $replaceDefaultExts, $replaceDefaultPatterns, $sourceBase]);
    }

    /**
     * {@inheritdoc}
     */
    public function addPackages(array $configs)
    {
        return $this->addConfig($configs, 'addPackage');
    }

    /**
     * {@inheritdoc}
     */
    public function removePackage($name)
    {
        $this->validate();
        unset($this->configPackages[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPackage($name)
    {
        $this->resolve();

        if (!$this->hasPackage($name)) {
            throw new InvalidConfigurationException(sprintf('The "%s" package does not exist', $name));
        }

        return $this->packages[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getPackages()
    {
        $this->resolve();

        return $this->packages;
    }

    /**
     * Resolves the configuration.
     */
    protected function resolve()
    {
        $this->locked = true;
        $defaultExts = $this->extManager->getDefaultExtensions();
        $defaultPatterns = $this->patternManager->getDefaultPatterns();

        foreach ($this->configPackages as $configs) {
            $package = PackageFactory::merge($configs, $defaultExts, $defaultPatterns);
            $this->packages[$package->getName()] = $package->getPackage();
        }

        $this->configPackages = [];
    }

    /**
     * {@inheritdoc}
     */
    protected function validate()
    {
        if ($this->locked) {
            throw new BadMethodCallException('PackageManager methods cannot be accessed when the manager is locked');
        }
    }
}
