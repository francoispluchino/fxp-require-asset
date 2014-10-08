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

/**
 * Config asset package.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class ConfigPackage extends Package implements ConfigPackageInterface
{
    /**
     * @var array
     */
    protected $unresolvedExts;

    /**
     * @var bool
     */
    protected $locked;

    /**
     * Constructor.
     *
     * @param string      $name       The asset package name
     * @param string      $sourcePath The source path of the package
     * @param string|null $sourceBase The custom source base using for the output path
     */
    public function __construct($name, $sourcePath, $sourceBase = null)
    {
        $this->name = $name;
        $this->sourcePath = $sourcePath;
        $this->sourceBase = $sourceBase;
        $this->unresolvedExts = array();
        $this->extensions = array();
        $this->patterns = array();
        $this->locked = false;
    }

    /**
     * {@inheritdoc}
     */
    public function addExtension(array $config)
    {
        if ($this->locked) {
            throw new BadMethodCallException('ConfigPackage methods cannot be accessed when the manager is locked');
        }

        if (!isset($config['name'])) {
            throw new InvalidArgumentException('The "name" key of package file extention config must be present');
        }

        $this->unresolvedExts[$config['name']][] = $config;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeExtension($name)
    {
        if ($this->locked) {
            throw new BadMethodCallException('ConfigPackage methods cannot be accessed when the manager is locked');
        }

        unset($this->extensions[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addPattern($pattern)
    {
        if ($this->locked) {
            throw new BadMethodCallException('ConfigPackage methods cannot be accessed when the manager is locked');
        }

        if (!$this->hasPattern($pattern)) {
            $this->patterns[] = $pattern;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removePattern($pattern)
    {
        if ($this->locked) {
            throw new BadMethodCallException('ConfigPackage methods cannot be accessed when the manager is locked');
        }

        if (false !== $pos = array_search($pattern, $this->patterns)) {
            array_splice($this->patterns, $pos, 1);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceBase()
    {
        if (null !== $this->sourceBase) {
            return $this->sourceBase;
        }

        return basename($this->getSourcePath());
    }

    /**
     * {@inheritdoc}
     */
    public function getPackage()
    {
        $this->locked = true;

        foreach ($this->unresolvedExts as $configs) {
            $conf = Utils::mergeConfigs($configs);
            $ext = FileExtensionFactory::create($conf);

            $this->extensions[$ext->getName()] = $ext;
        }

        $this->unresolvedExts = array();

        if (0 === count($this->patterns)) {
            $this->patterns[] = '*';
        }

        return new Package($this);
    }
}
