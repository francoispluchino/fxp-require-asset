<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\Node;

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
     * @param string      $name           The node name
     * @param string      $twigAssetClass The twig asset class name
     * @param int         $lineno         The lineno
     * @param string|null $position       The position in template
     * @param string      $tag            The twig tag
     */
    public function __construct($name, $twigAssetClass, $lineno, $position = null, $tag = null)
    {
        $twigAttributes = array(
            'name'           => $name,
            'twigAssetClass' => $twigAssetClass,
            'position'       => $position,
        );

        parent::__construct(array(), $twigAttributes, $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $name = $this->getAttribute('name');
        $twigAssetClass = $this->getAttribute('twigAssetClass');
        $position = $this->getAttribute('position');

        $compiler
            ->addDebugInfo($this)
            ->write(sprintf('$this->env->getExtension(\'%s\')->addAsset(new \%s(', 'fxp_require_asset', $twigAssetClass))
            ->raw(sprintf('array($this, \'%s\')', $name))
            ->raw(', ')->raw('$context')
            ->raw(', ')->raw('$blocks')
            ->raw(', ')->repr($position)
            ->raw(', ')->repr($this->getLine())
            ->raw(', ')->repr($compiler->getFilename())
            ->raw('));' . PHP_EOL);
        ;
    }
}
