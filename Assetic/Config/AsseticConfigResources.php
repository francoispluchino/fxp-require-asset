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
 * Assetic config resources.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AsseticConfigResources implements AsseticConfigResourcesInterface
{
    /**
     * @var AssetResourceInterface[]
     */
    protected $resources;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->resources = array();
    }

    /**
     * {@inheritdoc}
     */
    public function addResource(AssetResourceInterface $resource)
    {
        $this->resources[$resource->getName()] = $resource;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        return $this->resources;
    }
}
