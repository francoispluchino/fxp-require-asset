<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Webpack;

use Fxp\Component\RequireAsset\Asset\RequireAssetManagerInterface;
use Fxp\Component\RequireAsset\Exception\AssetNotFoundException;
use Fxp\Component\RequireAsset\Webpack\Adapter\AdapterInterface;

/**
 * Require asset manager for webpack.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class WebpackRequireAssetManager implements RequireAssetManagerInterface
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * Constructor.
     *
     * @param AdapterInterface $adapter The webpack plugin adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function has($asset, $type = null)
    {
        try {
            return (bool) $this->adapter->getPath($asset, $type);
        } catch (AssetNotFoundException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($asset, $type = null)
    {
        return $this->adapter->getPath($asset, $type);
    }
}
