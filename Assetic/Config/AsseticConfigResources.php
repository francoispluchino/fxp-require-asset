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

use Fxp\Component\RequireAsset\Assetic\Util\Utils;
use Fxp\Component\RequireAsset\Exception\InvalidArgumentException;

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
    public function hasResource($name)
    {
        return isset($this->resources[Utils::formatName($name)]);
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
    public function removeResource($name)
    {
        unset($this->resources[Utils::formatName($name)]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource($name)
    {
        if (!$this->hasResource($name)) {
            throw new InvalidArgumentException(sprintf('The "%s" config of asset resource does not exist', $name));
        }

        return $this->resources[Utils::formatName($name)];
    }

    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        return $this->resources;
    }
}
