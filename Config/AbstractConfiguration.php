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
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * This is the class that validates and merges configuration.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getNode()
    {
        return static::getNodeDefinition()->getNode(true);
    }

    /**
     * Create the root node.
     *
     * @param string $name The node name
     *
     * @return ArrayNodeDefinition
     */
    protected static function createRoot($name)
    {
        $treeBuilder = new TreeBuilder();
        /* @var ArrayNodeDefinition $node */
        $node = $treeBuilder->root($name);

        return $node;
    }
}
