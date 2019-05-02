<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Webpack\Adapter;

use Fxp\Component\RequireAsset\Exception\InvalidArgumentException;

/**
 * The base of webpack plugin adapter.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * Check if the asset is a webpack asset.
     *
     * @param string $asset The asset name
     *
     * @return bool
     */
    protected function isWebpackAsset($asset)
    {
        return 0 === strpos($asset, '@webpack/');
    }

    /**
     * Get the asset name without webpack prefix.
     *
     * @param string $asset The asset name
     *
     * @return string
     */
    protected function getAssetName($asset)
    {
        return $this->isWebpackAsset($asset)
            ? substr($asset, 9)
            : $asset;
    }

    /**
     * Get the asset type.
     *
     * @param string      $asset      The asset name
     * @param string[]    $availables The available types
     * @param null|string $type       The asset type
     *
     * @return string
     */
    protected function getAssetType($asset, array $availables, $type)
    {
        $type = null === $type
            ? $this->findAssetType($asset, $availables)
            : $this->formatAssetType($type);

        if (null !== $type) {
            return $type;
        }

        throw new InvalidArgumentException(sprintf('The asset type is required for the asset "%s"', $asset));
    }

    /**
     * Format the asset type.
     *
     * @param string $type The asset type
     *
     * @return string
     */
    protected function formatAssetType($type)
    {
        if ('script' === $type) {
            $type = 'js';
        } elseif ('style' === $type) {
            $type = 'css';
        }

        return $type;
    }

    /**
     * Find automatically the asset type.
     *
     * @param string   $asset      The asset name
     * @param string[] $availables The available types
     *
     * @return null|string
     */
    abstract protected function findAssetType($asset, array $availables);
}
