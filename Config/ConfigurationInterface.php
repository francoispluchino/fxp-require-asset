<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Config;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\NodeInterface;

/**
 * Interface of asset config.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface ConfigurationInterface
{
    /**
     * Get the node.
     *
     * @return NodeInterface
     */
    public static function getNode();

    /**
     * Get the node definition.
     *
     * @return ArrayNodeDefinition
     */
    public static function getNodeDefinition();
}
