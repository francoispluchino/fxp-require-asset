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
 * Asset Replacement Manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetReplacementManager implements AssetReplacementManagerInterface
{
    /**
     * @var array
     */
    protected $replacements = array();

    /**
     * {@inheritdoc}
     */
    public function addReplacement($assetName, $replacementName)
    {
        $this->replacements[Utils::formatName($assetName)] = array($assetName, $replacementName);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addReplacements(array $replacements)
    {
        foreach ($replacements as $assetName => $replacementName) {
            $this->addReplacement($assetName, $replacementName);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeReplacement($assetName)
    {
        unset($this->replacements[Utils::formatName($assetName)]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasReplacement($assetName)
    {
        return isset($this->replacements[Utils::formatName($assetName)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getReplacement($assetName)
    {
        if (!$this->hasReplacement($assetName)) {
            throw new InvalidArgumentException(sprintf('The "%s" asset replacement does not exist in require asset manager', $assetName));
        }

        return $this->replacements[Utils::formatName($assetName)][1];
    }

    /**
     * {@inheritdoc}
     */
    public function getReplacements()
    {
        $replacements = array();

        foreach ($this->replacements as $replacementConfig) {
            $replacements[$replacementConfig[0]] = $replacementConfig[1];
        }

        return $replacements;
    }
}
