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

use Fxp\Component\RequireAsset\Assetic\Factory\Config\FileExtensionFactory;
use Fxp\Component\RequireAsset\Exception\BadMethodCallException;

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
    public function hasExtension($name)
    {
        return isset($this->unresolvedExts[$name]) || parent::hasExtension($name);
    }

    /**
     * {@inheritdoc}
     */
    public function addExtension($name, array $options = array(), array $filters = array(), $extension = null, $debug = false, $exclude = false)
    {
        $this->validate();

        if (!$name instanceof FileExtensionInterface) {
            $name = $this->createFileExtension($name, $options, $filters, $extension, $debug, $exclude);
        }

        $this->unresolvedExts[$name->getName()][] = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeExtension($name)
    {
        $this->validate();
        unset($this->unresolvedExts[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addPattern($pattern)
    {
        $this->validate();

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
        $this->validate();

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

        /* @var FileExtensionInterface[] $configs */
        foreach ($this->unresolvedExts as $configs) {
            $ext = FileExtensionFactory::merge($configs);
            $this->extensions[$ext->getName()] = $ext;
        }

        $this->unresolvedExts = array();

        if (0 === count($this->patterns)) {
            $this->patterns[] = '*';
        }

        return new Package($this);
    }

    /**
     * Create the config of extension.
     *
     * @param string      $name      The name of extension or the file extension instance
     * @param array       $options   The assetic formulae options
     * @param array       $filters   The assetic formulae filters
     * @param string|null $extension The output extension
     * @param bool        $debug     The debug mode
     * @param bool        $exclude   Exclude or not the file extension
     *
     * @return FileExtensionInterface
     */
    protected function createFileExtension($name, array $options, array $filters, $extension, $debug, $exclude)
    {
        return FileExtensionFactory::create(array(
            'name'      => $name,
            'options'   => $options,
            'filters'   => $filters,
            'extension' => $extension === $name ? null : $extension,
            'debug'     => $debug,
            'exclude'   => $exclude,
        ));
    }

    /**
     * Validate the instance.
     *
     * @throws BadMethodCallException When the config package is locked
     */
    protected function validate()
    {
        if ($this->locked) {
            throw new BadMethodCallException('ConfigPackage methods cannot be accessed when the manager is locked');
        }
    }
}
