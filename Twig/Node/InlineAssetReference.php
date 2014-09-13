<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Twig\Node;

/**
 * Represents a inline asset node.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InlineAssetReference extends \Twig_Node
{
    /**
     * Constructor.
     *
     * @param string $name
     * @param string $type
     * @param int    $lineno
     * @param string $tag
     */
    public function __construct($name, $type, $lineno, $tag = null)
    {
        parent::__construct(array(), array('name' => $name, 'type' => $type), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write(sprintf("\$this->env->getExtension('%s')->addAsset('%s', array(\$this, '%s'), \$context, \$blocks);\n", 'fxp_require_asset_inline_asset', $this->getAttribute('type'), $this->getAttribute('name')))
        ;
    }
}
