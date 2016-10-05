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
 * Represents a require tag node.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireTagReference extends \Twig_Node
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
     * @param string|null $position   The require position in template
     * @param string|null $twigTag    The twig tag
     */
    public function __construct($extension, $name, $tagClass, array $assets, array $attributes, $lineno, $position = null, $twigTag = null)
    {
        $this->extension = $extension;
        $twigAttributes = array(
            'name' => $name,
            'tagClass' => $tagClass,
            'assets' => $assets,
            'attributes' => $attributes,
            'position' => $position,
        );

        parent::__construct(array(), $twigAttributes, $lineno, $twigTag);
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
     * @param \Twig_Compiler $compiler
     */
    protected function compileTags(\Twig_Compiler $compiler)
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
                ->raw(', ')->repr($this->getLine())
                ->raw(', ')->repr($compiler->getFilename())
                ->raw('))'.PHP_EOL)
            ;
        }
    }
}
