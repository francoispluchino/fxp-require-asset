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
 * Represents a require asset node.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetReference extends \Twig_Node
{
    /**
     * Constructor.
     *
     * @param string      $name           The node name
     * @param string      $twigAssetClass The twig asset class name
     * @param array       $assets         The assets
     * @param array       $attributes     The attributes of assets
     * @param int         $lineno         The lineno
     * @param string|null $position       The require position in template
     * @param string|null $tag            The twig tag
     */
    public function __construct($name, $twigAssetClass, array $assets, array $attributes, $lineno, $position = null, $tag = null)
    {
        $twigAttributes = array(
            'name'           => $name,
            'twigAssetClass' => $twigAssetClass,
            'assets'         => $assets,
            'attributes'     => $attributes,
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
        $compiler
            ->addDebugInfo($this)
            ->write(sprintf('$this->env->getExtension(\'%s\')', 'fxp_require_asset'))
            ->raw(PHP_EOL)
            ->indent()
        ;

        $this->compileAssets($compiler);

        $compiler
            ->outdent()
            ->raw(';' . PHP_EOL)
        ;
    }

    /**
     * Compile the assets.
     *
     * @param \Twig_Compiler $compiler
     */
    protected function compileAssets(\Twig_Compiler $compiler)
    {
        $twigAssetClass = $this->getAttribute('twigAssetClass');
        $assets = $this->getAttribute('assets');
        $attributes = $this->getAttribute('attributes');
        $position = $this->getAttribute('position');

        foreach ($assets as $asset) {
            $compiler
                ->write(sprintf('->addAsset(new \%s(', $twigAssetClass))
                ->repr($asset)
                ->raw(', ')
                ->repr($attributes)
                ->raw(', ')
                ->repr($position)
                ->raw('))' . PHP_EOL)
            ;
        }
    }
}
