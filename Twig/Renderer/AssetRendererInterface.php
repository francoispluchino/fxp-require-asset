<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\Renderer;

use Fxp\Component\RequireAsset\Twig\Asset\TwigAssetInterface;

/**
 * Interface of twig asset renderer.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface AssetRendererInterface
{
    /**
     * Render the asset.
     *
     * @param TwigAssetInterface $asset The twig asset
     *
     * @return string
     */
    public function render(TwigAssetInterface $asset);

    /**
     * Check if the renderer is valid for the twig asset.
     *
     * @param TwigAssetInterface $asset
     *
     * @return bool
     */
    public function validate(TwigAssetInterface $asset);

    /**
     * Reset the renderer.
     *
     * @return void
     */
    public function reset();
}
