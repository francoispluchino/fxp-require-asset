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
 * Represents a inline tag node.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InlineTagReference extends \Twig_Node
{
    /**
     * @var string
     */
    protected $extension;

    /**
     * Constructor.
     *
     * @param string      $extension The class name of twig extension
     * @param string      $name      The node name
     * @param string      $tagClass  The template tag classname
     * @param int         $lineno    The lineno
     * @param string|null $position  The position in template
     * @param string      $tag       The twig tag
     */
    public function __construct($extension, $name, $tagClass, $lineno, $position = null, $tag = null)
    {
        $this->extension = $extension;
        $twigAttributes = array(
            'name' => $name,
            'tagClass' => $tagClass,
            'position' => $position,
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
        $tagClass = $this->getAttribute('tagClass');
        $position = $this->getAttribute('position');

        $compiler
            ->addDebugInfo($this)
            ->write(sprintf('$this->env->getExtension(\'%s\')->addTag(new \%s(', $this->extension, $tagClass))
            ->raw('\Fxp\Component\RequireAsset\Twig\Tag\Renderer\InlineTagRendererUtils::renderBody(')
            ->raw(sprintf('array($this, \'%s\')', $name))
            ->raw(', ')->raw('$context')
            ->raw(', ')->raw('$blocks')
            ->raw(', ')->repr($this->getLine())
            ->raw(', ')->repr($this->getFilename())
            ->raw(')')
            ->raw(', ')->repr($position)
            ->raw(', ')->repr($this->getLine())
            ->raw(', ')->repr($this->getFilename())
            ->raw('));'.PHP_EOL);
    }
}
