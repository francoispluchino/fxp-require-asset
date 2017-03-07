<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\TwigFunction;

/**
 * Twig function for quickly create a template tag position.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class TagPositionFunction extends \Twig_Function
{
    /**
     * @var string|null
     */
    private $catagory;

    /**
     * @var string|null
     */
    private $type;

    /**
     * Constructor.
     *
     * @param string        $name
     * @param callable|null $callable
     * @param array         $options
     */
    public function __construct($name, $callable, array $options = array())
    {
        $options = array_merge($options, array(
            'node_class' => 'Fxp\Component\RequireAsset\Twig\Node\TagPositionFunctionNode',
            'is_safe' => array('html'),
            'category' => null,
            'type' => null,
        ), $options);
        $this->catagory = $options['category'];
        $this->type = $options['type'];

        parent::__construct($name, $callable, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return array_merge(array(
            $this->catagory,
            $this->type,
        ), parent::getArguments());
    }
}
