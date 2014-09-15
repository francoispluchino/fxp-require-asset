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

/**
 * Interface of asset definition cache.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface AssetCacheInterface
{
    /**
     * Checks if the cache has the asset definitions.
     *
     * @return bool
     */
    public function hasAssets();

    /**
     * Sets the asset definitions.
     *
     * @param AssetInterface[] $assets
     *
     * @return self
     */
    public function setAssets(array $assets);

    /**
     * Gets the asset definitions.
     *
     * @return array
     */
    public function getAssets();
}
