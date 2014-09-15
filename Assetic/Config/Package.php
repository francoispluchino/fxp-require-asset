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

use Fxp\Bundle\RequireAssetBundle\Exception\InvalidConfigurationException;

/**
 * Compiled config asset package.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class Package implements PackageInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $sourcePath;

    /**
     * @var array
     */
    protected $extensions;

    /**
     * @var array
     */
    protected $patterns;

    /**
     * Constructor.
     *
     * @param ConfigPackageInterface $config The config of this package
     */
    public function __construct(ConfigPackageInterface $config)
    {
        $this->name = $config->getName();
        $this->sourcePath = $config->getSourcePath();
        $this->extensions = $config->getExtensions();
        $this->patterns = $config->getPatterns();
    }

    /**
     * Clone instance.
     */
    public function __clone()
    {
        foreach ($this->extensions as $name => $ext) {
            $this->extensions[$name] = clone $ext;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourcePath()
    {
        return $this->sourcePath;
    }

    /**
     * {@inheritdoc}
     */
    public function hasExtension($name)
    {
        return isset($this->extensions[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension($name)
    {
        if ($this->hasExtension($name)) {
            return $this->extensions[$name];
        }

        throw new InvalidConfigurationException(sprintf('The "%s" package file extension does not exist'));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPattern($pattern)
    {
        return in_array($pattern, $this->patterns);
    }

    /**
     * {@inheritdoc}
     */
    public function getPatterns()
    {
        return $this->patterns;
    }
}
