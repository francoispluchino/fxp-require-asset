<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Asset;

use Fxp\Component\RequireAsset\Exception\AssetNotFoundException;

/**
 * Interface of require asset manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class ChainRequireAssetManager implements RequireAssetManagerInterface
{
    /**
     * @var RequireAssetManagerInterface[]
     */
    protected $managers;

    /**
     * Constructor.
     *
     * @param RequireAssetManagerInterface[] $managers The require asset managers
     */
    public function __construct(array $managers)
    {
        foreach ($managers as $manager) {
            $this->addRequireAssetManager($manager);
        }
    }

    /**
     * Add the require asset manager.
     *
     * @param RequireAssetManagerInterface $manager The require asset manager
     *
     * @return self
     */
    public function addRequireAssetManager(RequireAssetManagerInterface $manager)
    {
        $this->managers[] = $manager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($asset)
    {
        foreach ($this->managers as $manager) {
            if ($manager->has($asset)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($asset)
    {
        $previous = null;

        try {
            foreach ($this->managers as $manager) {
                if ($manager->has($asset)) {
                    return $manager->getPath($asset);
                }
            }
        } catch (\Exception $e) {
            $previous = $e;
        }

        throw new AssetNotFoundException($asset, 0, $previous);
    }
}
