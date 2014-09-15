<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Assetic\Cache;

use Fxp\Bundle\RequireAssetBundle\Assetic\AssetInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Asset definition cache.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetCache implements AssetCacheInterface
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
     * @var AssetInterface[]|null
     */
    protected $cacheData;

    /**
     * Constructor.
     *
     * @param string $filename The filename of cache file
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
        $this->fs = new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    public function hasAssets()
    {
        return null !== $this->cacheData || file_exists($this->filename);
    }

    /**
     * {@inheritdoc}
     */
    public function setAssets(array $assets)
    {
        $this->cacheData = $assets;
        $this->fs->dumpFile($this->filename, serialize($assets));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssets()
    {
        if (null !== $this->cacheData) {
            return $this->cacheData;
        }

        if ($this->hasAssets()) {
            $this->cacheData = unserialize(file_get_contents($this->filename));
        } else {
            $this->setAssets(array());
        }

        return $this->cacheData;
    }
}
