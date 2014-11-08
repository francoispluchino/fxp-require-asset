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
 * Node of twig asset function position.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetFunctionPosition extends \Twig_Node_Expression_Function
{
    /**
     * Compile.
     *
     * @param \Twig_Compiler $compiler
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $name = $this->getAttribute('name');
        $function = $compiler->getEnvironment()->getFunction($name);
        $function->setArguments(array($this->getLine(), $compiler->getFilename()));

        parent::compile($compiler);
    }
}
