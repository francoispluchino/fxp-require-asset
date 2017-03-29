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

use Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResourceInterface;

/**
 * Interface of asset resources cache.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface AsseticAssetCacheInterface
{
    /**
     * Checks if the cache has the asset resources.
     *
     * @return bool
     */
    public function hasResources();

    /**
     * Sets the asset resources.
     *
     * @param RequireAssetResourceInterface[] $assets
     *
     * @return self
     */
    public function setResources(array $assets);

    /**
     * Gets the asset resources.
     *
     * @return RequireAssetResourceInterface[]
     */
    public function getResources();

    /**
     * Invalidate the cache.
     *
     * @return self
     */
    public function invalidate();
}
