<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\Asset;

/**
 * Interface of twig asset configuration.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface TwigAssetInterface
{
    /**
     * Get the asset category.
     *
     * @return string
     */
    public function getCategory();

    /**
     * Get the asset type.
     *
     * @return string
     */
    public function getType();

    /**
     * Get the position.
     *
     * @return string|null
     */
    public function getPosition();

    /**
     * Get the tag position name of asset.
     *
     * @return string The formatted tag position
     */
    public function getTagPositionName();

    /**
     * Get the lineno.
     *
     * @return int
     */
    public function getLineno();

    /**
     * Get the twig filename.
     *
     * @return string|null
     */
    public function getFilename();
}
