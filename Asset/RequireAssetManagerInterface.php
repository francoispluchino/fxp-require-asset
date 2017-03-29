<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Asset;

use Fxp\Component\RequireAsset\Exception\AssetNotFoundException;

/**
 * Interface of require asset manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface RequireAssetManagerInterface
{
    /**
     * Check if the asset is present.
     *
     * @param string $asset The asset name
     *
     * @return bool
     */
    public function has($asset);

    /**
     * Get the public path of the asset.
     *
     * @param string $asset The asset name
     *
     * @return string
     *
     * @throws AssetNotFoundException When the asset is not found
     */
    public function getPath($asset);
}
