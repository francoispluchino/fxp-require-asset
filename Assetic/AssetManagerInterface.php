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

use Fxp\Bundle\RequireAssetBundle\Exception\InvalidArgumentException;

/**
 * Interface of asset manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface AssetManagerInterface
{
    /**
     * Checks if the asset is registred.
     *
     * @param string $name The asset name
     *
     * @return bool
     */
    public function hasAsset($name);

    /**
     * Gets the asset definition.
     *
     * @param string $name
     *
     * @return AssetInterface
     *
     * @throws InvalidArgumentException When the asset definition does not exist
     */
    public function getAsset($name);

    /**
     * Gets the asset definitions.
     *
     * @return AssetInterface[]
     */
    public function getAssets();

    /**
     * Checks if the asset definitions is compiled.
     *
     * @return bool
     */
    public function isCompiled();

    /**
     * Compiles the asset definitions.
     *
     * @return self
     */
    public function compile();
}
