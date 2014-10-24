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
use Fxp\Component\RequireAsset\Assetic\Util\FileExtensionUtils;
use Fxp\Component\RequireAsset\Exception\BadMethodCallException;

/**
 * Config asset package.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class ConfigPackage extends Package implements ConfigPackageInterface
{
    /**
     * @var bool
     */
    protected $replaceDefaultExtensions;

    /**
     * @var bool
     */
    protected $replaceDefaultPatterns;

    /**
     * @var bool
     */
    protected $locked;

    /**
     * Constructor.
     *
     * @param string      $name       The asset package name
     * @param string|null $sourcePath The source path of the package
     * @param string|null $sourceBase The custom source base using for the output path
     */
    public function __construct($name, $sourcePath = null, $sourceBase = null)
    {
        $this->name = $name;
        $this->sourcePath = $sourcePath;
        $this->sourceBase = $sourceBase;
        $this->extensions = array();
        $this->patterns = array();
        $this->replaceDefaultExtensions = false;
        $this->replaceDefaultPatterns = false;
        $this->locked = false;
    }

    /**
     * {@inheritdoc}
     */
    public function addExtension($name, array $options = array(), array $filters = array(), $extension = null, $debug = false, $exclude = false)
    {
        $this->validate();

        if (!$name instanceof FileExtensionInterface) {
            $name = FileExtensionUtils::createByConfig($name, $options, $filters, $extension, $debug, $exclude);
        }

        if ($this->hasExtension($name->getName())) {
            $name = FileExtensionFactory::merge(array($this->extensions[$name->getName()], $name));
        }

        $this->extensions[$name->getName()] = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeExtension($name)
    {
        $this->validate();
        unset($this->extensions[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setReplaceDefaultExtensions($replace)
    {
        $this->validate();
        $this->replaceDefaultExtensions = (bool) $replace;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function replaceDefaultExtensions()
    {
        return $this->replaceDefaultExtensions;
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
    public function setReplaceDefaultPatterns($replace)
    {
        $this->validate();
        $this->replaceDefaultPatterns = (bool) $replace;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function replaceDefaultPatterns()
    {
        return $this->replaceDefaultPatterns;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceBase()
    {
        return $this->sourceBase;
    }

    /**
     * {@inheritdoc}
     */
    public function getPackage()
    {
        $this->locked = true;

        if (0 === count($this->patterns)) {
            $this->patterns[] = '*';
        }

        return new Package($this);
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
