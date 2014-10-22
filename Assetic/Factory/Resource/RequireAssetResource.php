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

use Assetic\Factory\Resource\ResourceInterface;
use Fxp\Component\RequireAsset\Assetic\Util\Utils;

/**
 * Require asset resource.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetResource implements ResourceInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $formulaeName;

    /**
     * @var string
     */
    protected $sourcePath;

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
     * @param string $sourcePath The asset source path
     * @param string $targetPath The asset target path
     * @param array  $filters    The asset filters
     * @param array  $options    The asset filters
     */
    public function __construct($name, $sourcePath, $targetPath, array $filters = array(), array $options = array())
    {
        $this->name = $name;
        $this->formulaeName = Utils::formatName($name);
        $this->sourcePath = $sourcePath;
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
        return array(
            $this->formulaeName => array(
                // inputs
                array($this->sourcePath),
                // filters
                $this->filters,
                // options
                array_merge($this->options, array(
                    'output' => $this->targetPath,
                    'debug'  => false,
                )),
            ),
        );
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the source path.
     *
     * @return string
     */
    public function getSourcePath()
    {
        return $this->sourcePath;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return 'require_asset_resource_'.$this->formulaeName;
    }
}
