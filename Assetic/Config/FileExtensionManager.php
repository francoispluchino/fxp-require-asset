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

use Fxp\Bundle\RequireAssetBundle\Assetic\Factory\Config\FileExtensionFactory;
use Fxp\Bundle\RequireAssetBundle\Assetic\Util\Utils;
use Fxp\Bundle\RequireAssetBundle\Exception\BadMethodCallException;
use Fxp\Bundle\RequireAssetBundle\Exception\InvalidArgumentException;
use Fxp\Bundle\RequireAssetBundle\Exception\InvalidConfigurationException;

/**
 * Config file extension manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FileExtensionManager implements FileExtensionManagerInterface
{
    /**
     * @var array
     */
    protected $defaults;

    /**
     * @var array
     */
    protected $unresolvedDefaults;

    /**
     * @var bool
     */
    protected $locked;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->defaults = array();
        $this->unresolvedDefaults = array();
        $this->locked = false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDefaultExtension($name)
    {
        return isset($this->defaults[$name]) || isset($this->unresolvedDefaults[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function addDefaultExtension(array $config)
    {
        if ($this->locked) {
            throw new BadMethodCallException('FileExtensionManager methods cannot be accessed when the manager is locked');
        }

        if (!isset($config['name'])) {
            throw new InvalidArgumentException('The "name" key of file extention config must be present');
        }

        $this->unresolvedDefaults[$config['name']][] = $config;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addDefaultExtensions(array $configs)
    {
        foreach ($configs as $key => $config) {
            if (!isset($config['name'])) {
                $config['name'] = $key;
            }

            $this->addDefaultExtension($config);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeDefaultExtension($name)
    {
        if ($this->locked) {
            throw new BadMethodCallException('FileExtensionManager methods cannot be accessed when the manager is locked');
        }

        unset($this->defaults[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultExtension($name)
    {
        $this->resolve();

        if (!$this->hasDefaultExtension($name)) {
            throw new InvalidConfigurationException(sprintf('The "%s" default file extension does not exist'));
        }

        return $this->defaults[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultExtensions()
    {
        $this->resolve();

        return $this->defaults;
    }

    /**
     * Resolves the configuration.
     */
    protected function resolve()
    {
        $this->locked = true;

        foreach ($this->unresolvedDefaults as $configs) {
            $conf = Utils::mergeConfigs($configs);
            $ext = FileExtensionFactory::create($conf);

            $this->defaults[$ext->getName()] = $ext;
        }

        $this->unresolvedDefaults = array();
    }
}
