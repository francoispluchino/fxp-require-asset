<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\Asset\Conditional;

use Fxp\Component\RequireAsset\Twig\Asset\TwigAssetInterface;

/**
 * Interface of conditional render of twig asset configuration.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface ConditionalRenderInterface
{
    /**
     * Render the asset.
     *
     * @param TwigAssetInterface $asset The twig asset instance
     *
     * @return bool
     */
    public function isValid(TwigAssetInterface $asset);
}
