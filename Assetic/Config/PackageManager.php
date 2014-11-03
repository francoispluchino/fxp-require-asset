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
    public function addPackage($name, $sourcePath = null, array $extensions = array(), array $patterns = array(), $replaceDefaultExts = false, $replaceDefaultPatterns = false, $sourceBase = null)
    {
        $this->validate();

        $config = $this->createByConfig($name, $sourcePath, $extensions, $patterns, $replaceDefaultExts, $replaceDefaultPatterns, $sourceBase);
        $this->configPackages[$config->getName()][] = $config;

        return $this;
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

        $this->configPackages = array();
    }

    /**
     * Validate the instance.
     *
     * @throws BadMethodCallException When the config package is locked
     */
    protected function validate()
    {
        if ($this->locked) {
            throw new BadMethodCallException('PackageManager methods cannot be accessed when the manager is locked');
        }
    }

    /**
     * Create the config of asset package.
     *
     * @param string|array|ConfigPackageInterface $name                   The name of package or config or instance
     * @param string|null                         $sourcePath             The package source path
     * @param FileExtensionInterface[]|array      $extensions             The file extensions
     * @param string[]                            $patterns               The patterns
     * @param bool                                $replaceDefaultExts     Replace the default file extensions or add new file extensions
     * @param bool                                $replaceDefaultPatterns Replace the default patterns or add new patterns
     * @param string|null                         $sourceBase             The package source base
     *
     * @return ConfigPackageInterface
     */
    protected function createByConfig($name, $sourcePath = null, array $extensions = array(), array $patterns = array(), $replaceDefaultExts = false, $replaceDefaultPatterns = false, $sourceBase = null)
    {
        if (!$name instanceof ConfigPackageInterface) {
            $config = is_array($name) ? $name
                : array(
                    'name'                       => $name,
                    'source_path'                => $sourcePath,
                    'extensions'                 => $extensions,
                    'patterns'                   => $patterns,
                    'replace_default_extensions' => $replaceDefaultExts,
                    'replace_default_patterns'   => $replaceDefaultPatterns,
                    'source_base'                => $sourceBase,
                )
            ;

            $name = PackageFactory::createConfig($config);
        }

        return $name;
    }
}
