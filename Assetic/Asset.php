<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Assetic;

/**
 * Asset definition.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class Asset implements AssetInterface
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
     * @var string
     */
    protected $sourcePath;

    /**
     * @var string
     */
    protected $outputTarget;

    /**
     * Constructor.
     *
     * @param string $name         The asset name
     * @param array  $options      The assetic formulae options
     * @param array  $filters      The assetic formulae filters
     * @param string $sourcePath   The source path
     * @param string $outputTarget The output target
     */
    public function __construct($name, array $options, array $filters, $sourcePath, $outputTarget)
    {
        $this->name = $name;
        $this->options = $options;
        $this->filters = $filters;
        $this->sourcePath = $sourcePath;
        $this->outputTarget = $outputTarget;

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
    public function getSourcePath()
    {
        return $this->sourcePath;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputTarget()
    {
        return $this->outputTarget;
    }
}
