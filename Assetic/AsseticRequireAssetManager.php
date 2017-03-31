<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic;

use Assetic\Asset\AssetInterface;
use Assetic\Factory\LazyAssetManager;
use Fxp\Component\RequireAsset\Asset\RequireAssetManagerInterface;
use Fxp\Component\RequireAsset\Asset\Util\AssetUtils;
use Fxp\Component\RequireAsset\Exception\AssetNotFoundException;

/**
 * Require asset manager for Assetic.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AsseticRequireAssetManager implements RequireAssetManagerInterface
{
    /**
     * @var LazyAssetManager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param LazyAssetManager $manager The assetic manager
     */
    public function __construct(LazyAssetManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function has($asset, $type = null)
    {
        return $this->manager->has(AssetUtils::formatName($asset));
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($asset, $type = null)
    {
        $formattedName = AssetUtils::formatName($asset);

        if ($this->manager->has($formattedName)) {
            return $this->formatTargetPath($this->manager->get($formattedName));
        }

        throw new AssetNotFoundException($asset);
    }

    /**
     * Format the target path.
     *
     * @param AssetInterface $asset The assetic asset
     *
     * @return string
     */
    protected function formatTargetPath(AssetInterface $asset)
    {
        $target = str_replace('_controller/', '', $asset->getTargetPath());

        if (false === strpos($target, '://')) {
            $target = '/'.ltrim($target, '/');
        }

        return $target;
    }
}
