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
class ManifestAdapter extends AbstractAdapter
{
    /**
     * @var string
     */
    private $manifestPath;

    /**
     * @var array|null
     */
    private $manifestData;

    /**
     * Constructor.
     *
     * @param string $manifestPath The manifest path
     */
    public function __construct($manifestPath)
    {
        $this->manifestPath = $manifestPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($asset, $type = null)
    {
        $content = $this->getContent();
        $assetName = $this->getAssetName($asset);

        if ($this->isWebpackAsset($asset) && isset($content[$assetName])) {
            $type = $this->getAssetType($asset, ['js', 'css'], $type);

            if ($type === pathinfo($asset, PATHINFO_EXTENSION)) {
                return $content[$assetName];
            }
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

    /**
     * Get the content of assets file.
     *
     * @return array
     */
    private function getContent()
    {
        if (null === $this->manifestData) {
            if (!file_exists($this->manifestPath)) {
                throw new \RuntimeException(sprintf('Asset manifest file "%s" does not exist.', $this->manifestPath));
            }

            $this->manifestData = json_decode(file_get_contents($this->manifestPath), true);
            if (0 < json_last_error()) {
                throw new \RuntimeException(sprintf('Error parsing JSON from asset manifest file "%s" - %s', $this->manifestPath, json_last_error_msg()));
            }
        }

        return $this->manifestData;
    }
}
