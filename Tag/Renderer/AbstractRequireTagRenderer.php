<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tag\Renderer;

use Assetic\Asset\AssetInterface;
use Assetic\Asset\AssetReference;
use Assetic\Factory\LazyAssetManager;
use Assetic\Util\VarUtils;
use Fxp\Component\RequireAsset\Assetic\RequireLocaleManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Util\Utils;
use Fxp\Component\RequireAsset\Tag\RequireTagInterface;

/**
 * Abstract template require tag renderer.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractRequireTagRenderer implements TagRendererInterface
{
    /**
     * The list of already rendered tags.
     * @var array
     */
    protected $renderedTags;

    /**
     * @var LazyAssetManager
     */
    protected $manager;

    /**
     * @var RequireLocaleManagerInterface|null
     */
    protected $localeManager;

    /**
     * Constructor.
     *
     * @param LazyAssetManager                   $manager       The lazy assetic manager
     * @param RequireLocaleManagerInterface|null $localeManager The require locale asset manager
     */
    public function __construct(LazyAssetManager $manager, RequireLocaleManagerInterface $localeManager = null)
    {
        $this->manager = $manager;
        $this->localeManager = $localeManager;
        $this->reset();
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->renderedTags = array();
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
            $childName = Utils::formatName($localeAsset);
            $output .= $this->doRender($tag, $childName);
        }

        return $output;
    }

    /**
     * Do render the HTML tag.
     *
     * @param RequireTagInterface $tag
     * @param string              $asseticName The assetic name
     * @param AssetInterface|null $asset       The assetic asset (useful for common assets)
     *
     * @return string The output render
     */
    protected function doRender(RequireTagInterface $tag, $asseticName, AssetInterface $asset = null)
    {
        if ($this->canBeRendered($asseticName)) {
            $asset = null !== $asset ? $asset : $this->manager->get($asseticName);
            $attributes = $this->prepareAttributes($tag, $asset);
            $this->assetRendered($asseticName);

            return RequireUtil::renderHtmlTag($attributes, $tag->getHtmlTag(), $tag->shortEndTag());
        }

        return '';
    }

    /**
     * Prepare the attributes of HTML tag.
     *
     * @param RequireTagInterface $tag   The require tag
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
     * Get localized assets.
     *
     * @param string $asset The require asset
     *
     * @return string[]
     */
    protected function getLocalizedAssets($asset)
    {
        if (null !== $this->localeManager) {
            return $this->localeManager->getLocalizedAsset($asset);
        }

        return array();
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
     * Check if the asset can be rendered.
     *
     * @param string $asseticName The assetic name
     *
     * @return bool
     */
    protected function canBeRendered($asseticName)
    {
        return !in_array($asseticName, $this->renderedTags);
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
            $this->renderedTags[] = Utils::formatName($asset);
        }
    }
}
