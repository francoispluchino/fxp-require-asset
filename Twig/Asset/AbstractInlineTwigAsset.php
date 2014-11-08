<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\Asset;

use Fxp\Component\RequireAsset\Exception\TwigRuntimeException;
use Fxp\Component\RequireAsset\Twig\Asset\Conditional\ConditionalRenderInterface;

/**
 * Abstract config of twig inline asset.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractInlineTwigAsset extends AbstractTwigAsset
{
    /**
     * @var array
     */
    protected $callable;

    /**
     * @var array
     */
    protected $context;

    /**
     * @var array
     */
    protected $blocks;

    /**
     *
     * @param array       $callable The callable
     * @param array       $context  The twig context
     * @param array       $blocks   The twig blocks
     * @param string|null $position The position in the template
     */
    public function __construct(array $callable, array $context, array $blocks, $position = null)
    {
        parent::__construct($position);

        $this->callable = $callable;
        $this->context = $context;
        $this->blocks = $blocks;
    }

    /**
     * {@inheritDoc}
     */
    public function getCategory()
    {
        return 'inline';
    }

    /**
     * {@inheritDoc}
     */
    public function render(ConditionalRenderInterface $conditional = null)
    {
        $callable = $this->callable;

        if (2 !== count($callable) || !$callable[0] instanceof \Twig_Template || !is_string($callable[1])) {
            throw new TwigRuntimeException('The callable argument must be an array with Twig_Template instance and name function of the block to rendering');
        }

        return $callable[0]->renderBlock($callable[1], $this->context, $this->blocks);
    }
}
