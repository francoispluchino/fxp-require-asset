<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Assetic\Config;

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
     * @param string $name    The file extension name
     * @param array  $options The assetic formulae options
     * @param array  $filters The assetic formulae filters
     * @param bool   $debug   The debug mode
     * @param bool   $exclude This extension must be exclude or not
     */
    public function __construct($name, array $options = array(), array $filters = array(), $debug = false, $exclude = false)
    {
        $this->name = $name;
        $this->options = $options;
        $this->filters = $filters;
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
