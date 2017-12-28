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

/**
 * Require asset resource.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetResource extends AbstractRequireAssetResource
{
    /**
     * @var string
     */
    protected $sourcePath;

    /**
     * Constructor.
     *
     * @param string $name       The asset name
     * @param string $sourcePath The asset source path
     * @param string $targetPath The asset target path
     * @param array  $filters    The asset filters
     * @param array  $options    The asset filters
     */
    public function __construct($name, $sourcePath, $targetPath, array $filters = [], array $options = [])
    {
        parent::__construct($name, $targetPath, $filters, $options);

        $this->sourcePath = $sourcePath;
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
    public function getInputs()
    {
        return [$this->sourcePath];
    }

    /**
     * {@inheritdoc}
     */
    protected function getFixedOptions()
    {
        return ['debug' => false];
    }
}
