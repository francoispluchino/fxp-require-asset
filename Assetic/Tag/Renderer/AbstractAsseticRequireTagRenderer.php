<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic\Tag\Renderer;

use Assetic\Asset\AssetInterface;
use Assetic\Asset\AssetReference;
use Assetic\Factory\LazyAssetManager;
use Assetic\Util\VarUtils;
use Fxp\Component\RequireAsset\Asset\Config\LocaleManagerInterface;
use Fxp\Component\RequireAsset\Asset\Util\AssetUtils;
use Fxp\Component\RequireAsset\Tag\Renderer\BaseRequireTagRenderer;
use Fxp\Component\RequireAsset\Tag\Renderer\RequireUtil;
use Fxp\Component\RequireAsset\Tag\RequireTagInterface;

/**
 * Abstract template assetic require tag renderer.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractAsseticRequireTagRenderer extends BaseRequireTagRenderer
{
    /**
     * @var LazyAssetManager
     */
    protected $manager;

    /**
     * @var array
     */
    protected $debugCommonAssets;

    /**
     * Constructor.
     *
     * @param LazyAssetManager            $manager           The lazy assetic manager
     * @param LocaleManagerInterface|null $localeManager     The require locale asset manager
     * @param array                       $debugCommonAssets The common assets for debug mode without assetic common parts
     */
    public function __construct(LazyAssetManager $manager,
                                LocaleManagerInterface $localeManager = null,
                                array $debugCommonAssets = array())
    {
        $this->manager = $manager;
        $this->debugCommonAssets = $debugCommonAssets;

        parent::__construct($localeManager);
    }

    /**
     * Pre render the localized assets.
     *
     * @param RequireTagInterface $tag   The require template tag
     * @param string              $asset The asset name
     *
     * @return string The output render
     */
    protected function preRenderLocalized(RequireTagInterface $tag, $asset)
    {
        $output = '';

        foreach ($this->getLocalizedAssets($asset) as $localeAsset) {
            $childName = AssetUtils::formatName($localeAsset);
            $output .= $this->doRender($tag, $childName);
        }

        return $output;
    }

    /**
     * Do render the HTML tag.
     *
     * @param RequireTagInterface $tag       The require template tag
     * @param string              $assetName The asset name
     * @param AssetInterface|null $asset     The asset asset (useful for common assets)
     *
     * @return string The output render
     */
    protected function doRender(RequireTagInterface $tag, $assetName, AssetInterface $asset = null)
    {
        $output = '';

        if ($this->canBeRendered($assetName)) {
            $asset = null !== $asset ? $asset : $this->manager->get($assetName);
            $attributes = $this->prepareAttributes($tag, $asset);
            $this->assetRendered($assetName);

            $output = RequireUtil::renderHtmlTag($attributes, $tag->getHtmlTag(), $tag->shortEndTag());
        }

        return $output;
    }

    /**
     * Prepare the attributes of HTML tag.
     *
     * @param RequireTagInterface $tag   The require template tag
     * @param AssetInterface      $asset The assetic asset
     *
     * @return array
     */
    protected function prepareAttributes(RequireTagInterface $tag, AssetInterface $asset)
    {
        $target = $this->getTargetPath($asset);
        $attributes = $tag->getAttributes();
        $attributes[$tag->getLinkAttribute()] = $target;

        return $attributes;
    }

    /**
     * Get the target path of the asset.
     *
     * @param AssetInterface $asset The asset
     *
     * @return string
     */
    protected function getTargetPath(AssetInterface $asset)
    {
        $target = str_replace('_controller/', '', $asset->getTargetPath());
        $target = VarUtils::resolve($target, $asset->getVars(), $asset->getValues());

        return $target;
    }

    /**
     * Extract the assetic name of the asset reference.
     *
     * @param AssetReference $asset The asset
     *
     * @return string The assetic name
     */
    protected function extractAsseticName(AssetReference $asset)
    {
        $ref = new \ReflectionClass($asset);
        $meth = $ref->getProperty('name');
        $meth->setAccessible(true);

        return (string) $meth->getValue($asset);
    }

    /**
     * Indicate the asset is rendered.
     *
     * @param array|string $assets The asset name or list of asset name
     */
    protected function assetRendered($assets)
    {
        $assets = (array) $assets;

        foreach ($assets as $asset) {
            $this->renderedTags[] = AssetUtils::formatName($asset);
        }
    }
}
