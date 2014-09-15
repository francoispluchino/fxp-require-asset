<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Assetic\Config;

use Fxp\Bundle\RequireAssetBundle\Assetic\Factory\Config\PackageFactory;
use Fxp\Bundle\RequireAssetBundle\Assetic\Util\Utils;
use Fxp\Bundle\RequireAssetBundle\Exception\BadMethodCallException;
use Fxp\Bundle\RequireAssetBundle\Exception\InvalidArgumentException;
use Fxp\Bundle\RequireAssetBundle\Exception\InvalidConfigurationException;

/**
 * Config package manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class PackageManager implements PackageManagerInterface
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
        $this->configPackages = array();
        $this->packages = array();
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
    public function addPackage(array $config)
    {
        if ($this->locked) {
            throw new BadMethodCallException('PackageManager methods cannot be accessed when the manager is locked');
        }

        if (!isset($config['name'])) {
            throw new InvalidArgumentException('The "name" key of package config must be present');
        }

        $this->configPackages[$config['name']][] = $config;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addPackages(array $configs)
    {
        foreach ($configs as $key => $config) {
            if (!isset($config['name'])) {
                $config['name'] = $key;
            }

            $this->addPackage($config);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removePackage($name)
    {
        if ($this->locked) {
            throw new BadMethodCallException('PackageManager methods cannot be accessed when the manager is locked');
        }

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
            throw new InvalidConfigurationException(sprintf('The "%s" package does not exist'));
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
            $conf = Utils::mergeConfigs($configs);
            $package = PackageFactory::create($conf, $defaultExts, $defaultPatterns);

            $this->packages[$package->getName()] = $package;
        }

        $this->configPackages = array();
    }
}
