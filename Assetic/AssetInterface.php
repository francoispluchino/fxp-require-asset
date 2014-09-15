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

/**
 * Interface of asset definition.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface AssetInterface
{
    /**
     * Gets the asset name.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the assetic formulae options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Gets the assetic formulae filters.
     *
     * @return array
     */
    public function getFilters();

    /**
     * Gets the source path of the asset.
     *
     * @return string
     */
    public function getSourcePath();

    /**
     * Gets the output target of the asset.
     *
     * @return string
     */
    public function getOutputTarget();
}
