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

/**
 * Interface of assetic resource configurations (for asset manager).
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface AsseticConfigResourcesInterface
{
    /**
     * Add the config of the asset resource
     *
     * @param AssetResourceInterface $resource The config of asset resource
     *
     * @return self
     */
    public function addResource(AssetResourceInterface $resource);

    /**
     * Get the config of the asset resources.
     *
     * @return AssetResourceInterface[]
     */
    public function getResources();
}
