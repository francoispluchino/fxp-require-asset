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
 * Interface of twig require asset configuration.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface TwigRequireAssetInterface extends TwigContainerAwareInterface
{
    /**
     * Get the assetic name of this asset.
     *
     * @return string
     */
    public function getAsseticName();
}
