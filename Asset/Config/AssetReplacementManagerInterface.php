<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Asset\Config;

use Fxp\Component\RequireAsset\Exception\InvalidArgumentException;

/**
 * Interface of asset replacement manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface AssetReplacementManagerInterface
{
    /**
     * Add asset replacement.
     *
     * @param string $assetName       The asset name
     * @param string $replacementName The asset name of the replacement
     *
     * @return self
     */
    public function addReplacement($assetName, $replacementName);

    /**
     * Add asset replacements.
     *
     * @param array $replacements The map of asset name and asset replacement
     *
     * @return self
     */
    public function addReplacements(array $replacements);

    /**
     * Remove the asset replacement for an asset.
     *
     * @param string $assetName The asset name
     *
     * @return self
     */
    public function removeReplacement($assetName);

    /**
     * Check if an asset replacement exist for an asset.
     *
     * @param string $assetName The asset name
     *
     * @return bool
     */
    public function hasReplacement($assetName);

    /**
     * Get the asset replacement of asset.
     *
     * @param string $assetName The asset name
     *
     * @throws InvalidArgumentException When the asset replacement does not exist
     *
     * @return string
     */
    public function getReplacement($assetName);

    /**
     * Get the asset replacements.
     *
     * @return array
     */
    public function getReplacements();
}
