<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic\Config;

/**
 * Config file extension.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FileExtension implements FileExtensionInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var string|null
     */
    protected $outputExtension;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var bool
     */
    protected $exclude;

    /**
     * Constructor.
     *
     * @param string      $name            The file extension name
     * @param array       $options         The assetic formulae options
     * @param array       $filters         The assetic formulae filters
     * @param string|null $outputExtension The output format of extension
     * @param bool        $debug           The debug mode
     * @param bool        $exclude         This extension must be exclude or not
     */
    public function __construct($name, array $options = [], array $filters = [], $outputExtension = null, $debug = false, $exclude = false)
    {
        $this->name = $name;
        $this->options = $options;
        $this->filters = $filters;
        $this->outputExtension = $outputExtension;
        $this->debug = $debug;
        $this->exclude = $exclude;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputExtension()
    {
        return null !== $this->outputExtension ? $this->outputExtension : $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * {@inheritdoc}
     */
    public function isExclude()
    {
        return $this->exclude;
    }
}
