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

use Fxp\Component\RequireAsset\Exception\AssetNotFoundException;

/**
 * The manifest webpack plugin adapter.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class MockAdapter extends AbstractAdapter
{
    /**
     * {@inheritdoc}
     */
    public function getPath($asset, $type = null)
    {
        $foundType = $this->getAssetType($asset, ['js', 'css'], $type);

        if ($foundType === pathinfo($asset, PATHINFO_EXTENSION)) {
            return '/'.$this->getAssetName($asset);
        }

        throw new AssetNotFoundException($asset, $type);
    }

    /**
     * {@inheritdoc}
     */
    protected function findAssetType($asset, array $availables)
    {
        $ext = pathinfo($asset, PATHINFO_EXTENSION);

        return \in_array($ext, $availables) ? $ext : null;
    }
}
