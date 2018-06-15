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

use Assetic\Factory\LazyAssetManager;
use Fxp\Component\RequireAsset\Asset\Config\AssetReplacementManagerInterface;
use Fxp\Component\RequireAsset\Asset\Config\LocaleManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Tag\Renderer\AsseticRequireTagRenderer;
use Fxp\Component\RequireAsset\Tag\Renderer\InlineTagRenderer;
use Fxp\Component\RequireAsset\Webpack\Adapter\AssetsAdapter;
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
     * @param LazyAssetManager|null                 $manager            The lazy assetic manager
     * @param LocaleManagerInterface|null           $localeManager      The require locale asset manager
     * @param AssetReplacementManagerInterface|null $replacementManager The asset replacement manager
     * @param array                                 $debugCommonAssets  The common assets for debug mode without assetic common parts
     * @param string|null                           $webpackAssetsFile  The filename of webpack assets
     */
    public function __construct(LazyAssetManager $manager = null,
                                LocaleManagerInterface $localeManager = null,
                                AssetReplacementManagerInterface $replacementManager = null,
                                array $debugCommonAssets = [],
                                $webpackAssetsFile = null)
    {
        parent::__construct($replacementManager);

        $renderers = [new InlineTagRenderer()];

        if (null !== $webpackAssetsFile) {
            $wpManager = new WebpackRequireAssetManager(new AssetsAdapter($webpackAssetsFile));
            $renderers[] = new WebpackRequireTagRenderer($wpManager, $localeManager);
        }

        if (null !== $manager) {
            $renderers[] = new AsseticRequireTagRenderer($manager, $localeManager, $debugCommonAssets);
        }

        $this->setRenderers($renderers);
    }
}
