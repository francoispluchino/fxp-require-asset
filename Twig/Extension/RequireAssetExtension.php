<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\Extension;

use Assetic\Asset\AssetInterface;
use Assetic\Factory\LazyAssetManager;
use Fxp\Component\RequireAsset\Assetic\Util\Utils;

/**
 * RequireAssetExtension extends Twig with global assets rendering capabilities.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetExtension extends \Twig_Extension
{
    /**
     * @var LazyAssetManager|null
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param LazyAssetManager|null $manager The lazy assetic manager
     */
    public function __construct(LazyAssetManager $manager = null)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('requireAsset', array($this, 'requireAsset')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fxp_require_asset_url';
    }

    /**
     * Get the target path of the require asset.
     *
     * @param string $asset The require asset name
     *
     * @return string
     */
    public function requireAsset($asset)
    {
        $searchAsset = Utils::formatName($asset);

        return null !== $this->manager && $this->manager->has($searchAsset)
            ? $this->formatTargetPath($this->manager->get($searchAsset))
            : $asset;
    }

    /**
     * Format the target path.
     *
     * @param AssetInterface $asset
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
