<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Webpack;

use Fxp\Component\RequireAsset\Asset\RequireAssetManagerInterface;
use Fxp\Component\RequireAsset\Exception\AssetNotFoundException;
use Fxp\Component\RequireAsset\Exception\InvalidArgumentException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Require asset manager for webpack.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class WebpackRequireAssetManager implements RequireAssetManagerInterface
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var CacheItemPoolInterface|null
     */
    protected $cache;

    /**
     * @var string
     */
    protected $cacheKey;

    /**
     * @var array|null
     */
    protected $contentCache;

    /**
     * Constructor.
     *
     * @param string                      $filename The filename of webpack assets
     * @param CacheItemPoolInterface|null $cache    The cache
     * @param string                      $cacheKey The key for the cache
     */
    public function __construct($filename, CacheItemPoolInterface $cache = null, $cacheKey = 'fxp_require_asset_webpack_assets')
    {
        $this->filename = $filename;
        $this->cache = $cache;
        $this->cacheKey = $cacheKey;
    }

    /**
     * {@inheritdoc}
     */
    public function has($asset, $type = null)
    {
        try {
            return (bool) $this->getPath($asset, $type);
        } catch (AssetNotFoundException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($asset, $type = null)
    {
        $content = $this->getContent();
        $assetName = $this->getAssetName($asset);

        if ($this->isWebpackAsset($asset) && isset($content[$assetName])) {
            $assetData = $content[$assetName];
            $type = $this->getAssetType($asset, array_keys($assetData), $type);

            if (isset($assetData[$type])) {
                return $assetData[$type];
            }
        }

        throw new AssetNotFoundException($asset, $type);
    }

    /**
     * Get the content of assets file.
     *
     * @return array
     */
    protected function getContent()
    {
        if (null === $this->contentCache) {
            $item = $this->getCachedContent();

            if (null === $this->contentCache) {
                $this->contentCache = $this->saveContentInCache($item, $this->readFile());
            }
        }

        return $this->contentCache;
    }

    /**
     * Get the content of the json assets file.
     *
     * @return array
     */
    protected function readFile()
    {
        $content = @file_get_contents($this->filename);

        if (false === $content) {
            throw new InvalidArgumentException(sprintf('Cannot access "%s" to read the JSON file', $this->filename));
        }

        $content = json_decode($content, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(sprintf('Cannot read the JSON content: %s', json_last_error_msg()));
        }

        return is_array($content) ? $content : array();
    }

    /**
     * Check if the asset is a webpack asset.
     *
     * @param string $asset The asset name
     *
     * @return bool
     */
    protected function isWebpackAsset($asset)
    {
        return 0 === strpos($asset, '@webpack/');
    }

    /**
     * Get the asset name without webpack prefix.
     *
     * @param string $asset The asset name
     *
     * @return string
     */
    protected function getAssetName($asset)
    {
        return 0 === strpos($asset, '@webpack/')
            ? substr($asset, 9)
            : $asset;
    }

    /**
     * Get the asset type.
     *
     * @param string      $asset      The asset name
     * @param string[]    $availables The available types
     * @param string|null $type       The asset type
     *
     * @return string
     */
    protected function getAssetType($asset, array $availables, $type)
    {
        $type = null === $type
            ? $this->findAssetType($availables)
            : $this->formatAssetType($type);

        if (null !== $type) {
            return $type;
        }

        throw new InvalidArgumentException(sprintf('The asset type is required for the asset "%s"', $asset));
    }

    /**
     * Format the asset type.
     *
     * @param string $type The asset type
     *
     * @return string
     */
    private function formatAssetType($type)
    {
        if ('script' === $type) {
            $type = 'js';
        } elseif ('style' === $type) {
            $type = 'css';
        }

        return $type;
    }

    /**
     * Find automatically the asset type.
     *
     * @param string[] $availables The available types
     *
     * @return string|null
     */
    private function findAssetType(array $availables)
    {
        $type = null;

        if (in_array('css', $availables)) {
            $type = 'css';
        } elseif (1 === count($availables)) {
            $type = current($availables);
        }

        return $type;
    }

    /**
     * Get the cached content.
     *
     * @return CacheItemInterface|null
     */
    private function getCachedContent()
    {
        $item = null;

        if (null !== $this->cache) {
            $item = $this->cache->getItem($this->cacheKey);
            $cacheContent = $item->get();

            if ($item->isHit() && null !== $cacheContent) {
                $this->contentCache = $cacheContent;
            }
        }

        return $item;
    }

    /**
     * Save the content in the cache and returns the content.
     *
     * @param CacheItemInterface|null $item    The cache item
     * @param array                   $content The content
     *
     * @return array
     */
    private function saveContentInCache($item, array $content)
    {
        if (null !== $this->cache && null !== $item) {
            $item->set($this->contentCache);
            $this->cache->save($item);
        }

        return $content;
    }
}
