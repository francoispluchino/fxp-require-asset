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
use Twig\Node\Expression\FunctionExpression;

/**
 * Node of twig function of template tag position.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class TagPositionFunctionNode extends FunctionExpression
{
    /**
     * Compile.
     *
     * @param Compiler $compiler
     */
    public function compile(Compiler $compiler): void
    {
        $name = $this->getAttribute('name');
        $function = $compiler->getEnvironment()->getFunction($name);
        $arguments = $function->getArguments();

        if (\count($arguments) >= 2) {
            $arguments = [$arguments[0], $arguments[1], $this->getTemplateLine(), $this->getTemplateName()];
        }

        $function->setArguments($arguments);

        parent::compile($compiler);
    }
}
