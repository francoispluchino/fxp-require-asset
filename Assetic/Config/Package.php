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

use Fxp\Bundle\RequireAssetBundle\Exception\InvalidArgumentException;
use Fxp\Bundle\RequireAssetBundle\Exception\InvalidConfigurationException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\Glob;

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
     * @var string
     */
    protected $sourceBase;

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
        $this->sourceBase = $config->getSourceBase();
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
    public function getSourceBase()
    {
        return $this->sourceBase;
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

    /**
     * {@inheritdoc}
     */
    public function getFiles($debug = false)
    {
        $finder = Finder::create()->ignoreVCS(true)->ignoreDotFiles(true);

        try {
            $finder->in($this->getSourcePath());

        } catch (\InvalidArgumentException $ex) {
            throw new InvalidArgumentException(sprintf('The source path ("%s") of the asset package "%s" does not exist', $this->getSourcePath(), $this->getName()));
        }

        $this->putFileExtensionFilters($finder, $debug);
        $this->putPatternFilters($finder);

        return $finder->files();
    }

    /**
     * Puts the filters of file extensions in finder.
     *
     * @param Finder $finder The finder instance
     * @param bool   $debug  The debug mode
     */
    protected function putFileExtensionFilters(Finder $finder, $debug)
    {
        foreach ($this->getExtensions() as $ext) {
            $pattern = Glob::toRegex('*.' . $ext->getName(), true, false);
            $method = 'name';

            if ($ext->isExclude() || ($ext->isDebug() && !$debug)) {
                $method = 'notName';
            }

            $finder->$method($pattern);
        }
    }

    /**
     * Puts the filters of patterns in finder.
     *
     * @param Finder $finder The finder instance
     */
    protected function putPatternFilters(Finder $finder)
    {
        foreach ($this->getPatterns() as $pattern) {
            if (0 === strpos($pattern, '!')) {
                $finder->notPath(Glob::toRegex(substr($pattern, 1), true, false));

            } else {
                $finder->path(Glob::toRegex($pattern, true, false));
            }
        }
    }
}
