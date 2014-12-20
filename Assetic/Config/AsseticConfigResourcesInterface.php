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

use Fxp\Component\RequireAsset\Exception\InvalidArgumentException;

/**
 * Interface of assetic resource configurations (for asset manager).
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface AsseticConfigResourcesInterface
{
    /**
     * Check if the config of the asset resource is present.
     *
     * @param string $name The asset resource name
     *
     * @return bool
     */
    public function hasResource($name);

    /**
     * Add the config of the asset resource.
     *
     * @param AssetResourceInterface $resource The config of asset resource
     *
     * @return self
     */
    public function addResource(AssetResourceInterface $resource);

    /**
     * Remove the config of the asset resource.
     *
     * @param string $name The asset resource name
     *
     * @return self
     */
    public function removeResource($name);

    /**
     * Get the config of the asset resource.
     *
     * @param string $name The asset resource name
     *
     * @return AssetResourceInterface
     *
     * @throws InvalidArgumentException When the config of asset resource does not exist
     */
    public function getResource($name);

    /**
     * Get the config of the asset resources.
     *
     * @return AssetResourceInterface[]
     */
    public function getResources();
}
