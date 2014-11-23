<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic\Cache;

use Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResource;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Asset resources cache.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetCache implements RequireAssetCacheInterface
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var RequireAssetResource[]|null
     */
    protected $cacheData;

    /**
     * Constructor.
     *
     * @param string $dir      The directory of cache
     * @param string $filename The filename of cache file
     */
    public function __construct($dir, $filename = 'require-assets')
    {
        $this->filename = rtrim($dir, '/').DIRECTORY_SEPARATOR.$filename;
        $this->fs = new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    public function hasResources()
    {
        return null !== $this->cacheData || file_exists($this->filename);
    }

    /**
     * {@inheritdoc}
     */
    public function setResources(array $assets)
    {
        $this->cacheData = $assets;
        $this->fs->dumpFile($this->filename, serialize($assets));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        if (null !== $this->cacheData) {
            return $this->cacheData;
        }

        if ($this->hasResources()) {
            $this->cacheData = unserialize(file_get_contents($this->filename));
        } else {
            $this->setResources(array());
        }

        return $this->cacheData;
    }

    /**
     * {@inheritdoc}
     */
    public function invalidate()
    {
        $this->cacheData = null;
        $this->fs->remove($this->filename);

        return $this;
    }
}
