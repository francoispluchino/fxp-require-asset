<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic\Factory\Resource;

use Fxp\Component\RequireAsset\Asset\Util\AssetUtils;

/**
 * Abstract require asset resource.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractRequireAssetResource implements RequireAssetResourceInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $prettyName;

    /**
     * @var string
     */
    protected $targetPath;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param string $name       The asset name
     * @param string $targetPath The asset target path
     * @param array  $filters    The asset filters
     * @param array  $options    The asset filters
     */
    public function __construct($name, $targetPath, array $filters = array(), array $options = array())
    {
        $this->name = AssetUtils::formatName($name);
        $this->prettyName = $name;
        $this->targetPath = $targetPath;
        $this->filters = $filters;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($timestamp)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormulae()
    {
        return array(
            $this->name => array(
                // inputs
                $this->getInputs(),
                // filters
                $this->filters,
                // options
                array_merge($this->options, $this->getFixedOptions(), array(
                    'output' => $this->targetPath,
                )),
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPrettyName()
    {
        return $this->prettyName;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetPath()
    {
        return $this->targetPath;
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
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Get the default options.
     *
     * @return array
     */
    abstract protected function getFixedOptions();
}
