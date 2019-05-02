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

use Fxp\Component\RequireAsset\Asset\Config\AssetReplacementManagerInterface;
use Fxp\Component\RequireAsset\Asset\Config\LocaleManagerInterface;
use Fxp\Component\RequireAsset\Tag\Renderer\InlineTagRenderer;
use Fxp\Component\RequireAsset\Webpack\Adapter\AssetsAdapter;
use Fxp\Component\RequireAsset\Webpack\Adapter\ManifestAdapter;
use Fxp\Component\RequireAsset\Webpack\Tag\Renderer\WebpackRequireTagRenderer;
use Fxp\Component\RequireAsset\Webpack\WebpackRequireAssetManager;

/**
 * Core asset extension.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class CoreAssetExtension extends AssetExtension
{
    /**
     * Constructor.
     *
     * @param null|string                           $webpackFile        The filename of webpack manifest or assets
     * @param string                                $webpackAdapter     The webpack adapter (manifest of assets)
     * @param null|LocaleManagerInterface           $localeManager      The require locale asset manager
     * @param null|AssetReplacementManagerInterface $replacementManager The asset replacement manager
     */
    public function __construct(
        $webpackFile = null,
        $webpackAdapter = 'manifest',
        LocaleManagerInterface $localeManager = null,
        AssetReplacementManagerInterface $replacementManager = null
    ) {
        parent::__construct($replacementManager);

        $renderers = [new InlineTagRenderer()];

        if (null !== $webpackFile) {
            $adapter = 'manifest' === $webpackAdapter ? new ManifestAdapter($webpackFile) : new AssetsAdapter($webpackFile);
            $wpManager = new WebpackRequireAssetManager($adapter);
            $renderers[] = new WebpackRequireTagRenderer($wpManager, $localeManager);
        }

        $this->setRenderers($renderers);
    }
}
