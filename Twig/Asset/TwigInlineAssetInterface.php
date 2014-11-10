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
 * Interface of twig inline asset configuration.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface TwigInlineAssetInterface extends TwigAssetInterface
{
    /**
     * Get the callable.
     *
     * @return array
     */
    public function getCallable();

    /**
     * Get the context
     *
     * @return array
     */
    public function getContext();

    /**
     * Get the blocks.
     *
     * @return array
     */
    public function getBlocks();
}
