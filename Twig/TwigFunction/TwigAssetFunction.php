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
 * Twig function for create quickly a asset position.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class TwigAssetFunction extends \Twig_SimpleFunction
{
    /**
     * Constructor.
     *
     * @param string       $name
     * @param string|array $callable
     * @param array        $options
     */
    public function __construct($name, $callable, array $options = array())
    {
        parent::__construct($name, $callable, $options);

        $this->options = array_merge($this->options, array(
            'node_class' => 'Fxp\Component\RequireAsset\Twig\Node\AssetFunctionPosition',
            'is_safe'    => array('html'),
            'category'   => null,
            'type'       => null,
        ), $options);
    }

    /**
     * Get arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return array_merge(array(
            $this->options['category'],
            $this->options['type'],
        ), $this->arguments);
    }
}
