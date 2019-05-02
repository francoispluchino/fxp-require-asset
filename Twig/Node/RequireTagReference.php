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

use Twig\Compiler;
use Twig\Node\Node;

/**
 * Represents a require tag node.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireTagReference extends Node
{
    /**
     * @var string
     */
    protected $extension;

    /**
     * Constructor.
     *
     * @param string      $extension  The class name of twig extension
     * @param string      $name       The node name
     * @param string      $tagClass   The template tag classname
     * @param array       $assets     The assets
     * @param array       $attributes The attributes of tags
     * @param int         $lineno     The template lineno
     * @param null|string $position   The require position in template
     * @param null|string $twigTag    The twig tag
     */
    public function __construct($extension, $name, $tagClass, array $assets, array $attributes, $lineno, $position = null, $twigTag = null)
    {
        $this->extension = $extension;
        $twigAttributes = [
            'name' => $name,
            'tagClass' => $tagClass,
            'assets' => $assets,
            'attributes' => $attributes,
            'position' => $position,
        ];

        parent::__construct([], $twigAttributes, $lineno, $twigTag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Compiler $compiler A Twig_Compiler instance
     */
    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->write(sprintf('$this->env->getExtension(\'%s\')', $this->extension))
            ->raw(PHP_EOL)
            ->indent()
        ;

        $this->compileTags($compiler);

        $compiler
            ->outdent()
            ->raw(';'.PHP_EOL)
        ;
    }

    /**
     * Compile the tags.
     *
     * @param Compiler $compiler
     */
    protected function compileTags(Compiler $compiler): void
    {
        $tagClass = $this->getAttribute('tagClass');
        $assets = $this->getAttribute('assets');
        $attributes = $this->getAttribute('attributes');
        $position = $this->getAttribute('position');

        foreach ($assets as $asset) {
            $compiler
                ->write(sprintf('->addTag(new \%s(', $tagClass))
                ->repr($asset)
                ->raw(', ')->repr($attributes)
                ->raw(', ')->repr($position)
                ->raw(', ')->repr($this->getTemplateLine())
                ->raw(', ')->repr($this->getTemplateName())
                ->raw('))'.PHP_EOL)
            ;
        }
    }
}
