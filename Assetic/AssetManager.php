<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Assetic;

use Fxp\Bundle\RequireAssetBundle\Assetic\Cache\AssetCacheInterface;
use Fxp\Bundle\RequireAssetBundle\Assetic\Config\PackageInterface;
use Fxp\Bundle\RequireAssetBundle\Assetic\Config\PackageManagerInterface;
use Fxp\Bundle\RequireAssetBundle\Assetic\Factory\AssetFactory;
use Fxp\Bundle\RequireAssetBundle\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\Glob;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Asset manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetManager implements AssetManagerInterface
{
    /**
     * @var PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @var AssetCacheInterface
     */
    protected $cache;

    /**
     * @var string
     */
    protected $output;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * Constructor.
     *
     * @param PackageManagerInterface $packageManager
     * @param AssetCacheInterface     $cache
     * @param string                  $output
     * @param bool                    $debug
     */
    public function __construct(PackageManagerInterface $packageManager, AssetCacheInterface $cache, $output, $debug = false)
    {
        $this->packageManager = $packageManager;
        $this->cache = $cache;
        $this->output = trim($output, '/');
        $this->debug = $debug;
        $this->fs = new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    public function hasAsset($name)
    {
        $this->mustBeCompiled();
        $assets = $this->getAssets();

        return isset($assets[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAsset($name)
    {
        if ($this->hasAsset($name)) {
            $assets = $this->getAssets();

            return $assets[$name];
        }

        throw new InvalidArgumentException(sprintf('The require asset definition "%s" does not exist'));
    }

    /**
     * {@inheritdoc}
     */
    public function getAssets()
    {
        $this->mustBeCompiled();

        return $this->cache->getAssets();
    }

    /**
     * {@inheritdoc}
     */
    public function isCompiled()
    {
        return $this->cache->hasAssets();
    }

    /**
     * {@inheritdoc}
     */
    public function compile()
    {
        $packages = $this->packageManager->getPackages();
        $assets = array();

        foreach ($packages as $package) {
            $pAssets = $this->getPackageAssets($package);
            $assets = array_merge($assets, $pAssets);
        }

        $this->cache->setAssets($assets);

        return $this;
    }

    /**
     * @return self
     */
    protected function mustBeCompiled()
    {
        if (!$this->isCompiled()) {
            $this->compile();
        }

        return $this;
    }

    /**
     * Gets the assets of packages.
     *
     * @param PackageInterface $package The asset package instance
     *
     * @return array
     *
     * @throws InvalidArgumentException When the source path of asset package does not exist
     */
    protected function getPackageAssets(PackageInterface $package)
    {
        $finder = Finder::create()->ignoreVCS(true)->ignoreDotFiles(true);
        $assets = array();

        try {
            $finder->in($package->getSourcePath());

        } catch (\InvalidArgumentException $ex) {
            throw new InvalidArgumentException(sprintf('The source path ("%s") of the asset package "%s" does not exist', $package->getSourcePath(), $package->getName()));
        }

        $this->putFileExtensionFilters($package, $finder);
        $this->putPatternFilters($package, $finder);

        /* @var SplFileInfo $file */
        foreach ($finder->files() as $file) {
            $asset = AssetFactory::create($package, $file, $this->output, $this->fs);
            $assets[$asset->getName()] = $asset;
        }

        return $assets;
    }

    /**
     * Puts the filters of file extensions in finder.
     *
     * @param PackageInterface $package The asset package instance
     * @param Finder           $finder  The finder instance
     */
    private function putFileExtensionFilters(PackageInterface $package, Finder $finder)
    {
        foreach ($package->getExtensions() as $ext) {
            $pattern = Glob::toRegex('*.' . $ext->getName(), true, false);
            $method = 'name';

            if ($ext->isExclude() || ($ext->isDebug() && !$this->debug)) {
                $method = 'notName';
            }

            $finder->$method($pattern);
        }
    }

    /**
     * Puts the filters of patterns in finder.
     *
     * @param PackageInterface $package The asset package instance
     * @param Finder           $finder  The finder instance
     */
    private function putPatternFilters(PackageInterface $package, Finder $finder)
    {
        foreach ($package->getPatterns() as $pattern) {
            if (0 === strpos($pattern, '!')) {
                $finder->notPath(Glob::toRegex(substr($pattern, 1), true, false));

            } else {
                $finder->path(Glob::toRegex($pattern, true, false));
            }
        }
    }
}
