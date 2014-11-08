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
use Fxp\Component\RequireAsset\Twig\Asset\TwigRequireAssetInterface;

/**
 * Unique require asset of conditional render.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class UniqueRequireAsset implements ConditionalRenderInterface
{
    /**
     * The list of already rendered assets.
     * @var array
     */
    protected $renderedAssets = array();

    /**
     * {@inheritDoc}
     */
    public function isValid(TwigAssetInterface $asset)
    {
        if ($asset instanceof TwigRequireAssetInterface) {
            if (in_array($asset->getAsseticName(), $this->renderedAssets)) {
                return false;
            }

            $this->renderedAssets[] = $asset->getAsseticName();
        }

        return true;
    }
}
