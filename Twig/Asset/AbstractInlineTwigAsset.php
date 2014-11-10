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

/**
 * Abstract config of twig inline asset.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractInlineTwigAsset extends AbstractTwigAsset implements TwigInlineAssetInterface
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
     * Constructor.
     *
     * @param array       $callable The callable
     * @param array       $context  The twig context
     * @param array       $blocks   The twig blocks
     * @param string|null $position The position in the template
     * @param int         $lineno   The twig lineno
     * @param string|null $filename The twig filename
     */
    public function __construct(array $callable, array $context, array $blocks, $position = null, $lineno = -1, $filename = null)
    {
        parent::__construct($position, $lineno, $filename);

        $this->callable = $callable;
        $this->context = $context;
        $this->blocks = $blocks;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory()
    {
        return 'inline';
    }

    /**
     * {@inheritdoc}
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlocks()
    {
        return $this->blocks;
    }
}
