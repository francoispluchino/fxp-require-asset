<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Webpack\Tag\Renderer;

use Fxp\Component\RequireAsset\Asset\Config\LocaleManagerInterface;
use Fxp\Component\RequireAsset\Exception\RequireTagRendererException;
use Fxp\Component\RequireAsset\Tag\Renderer\BaseRequireTagRenderer;
use Fxp\Component\RequireAsset\Tag\Renderer\RequireUtil;
use Fxp\Component\RequireAsset\Tag\RequireTagInterface;
use Fxp\Component\RequireAsset\Tag\TagInterface;
use Fxp\Component\RequireAsset\Webpack\WebpackRequireAssetManager;

/**
 * Template webpack require tag renderer.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class WebpackRequireTagRenderer extends BaseRequireTagRenderer
{
    /**
     * @var WebpackRequireAssetManager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param WebpackRequireAssetManager  $manager       The webpack require asset manager
     * @param null|LocaleManagerInterface $localeManager The require locale asset manager
     */
    public function __construct(
        WebpackRequireAssetManager $manager,
        LocaleManagerInterface $localeManager = null
    ) {
        $this->manager = $manager;

        parent::__construct($localeManager);
    }

    /**
     * {@inheritdoc}
     */
    public function order(array $tags)
    {
        return $tags;
    }

    /**
     * {@inheritdoc}
     */
    public function render(TagInterface $tag)
    {
        /* @var RequireTagInterface $tag */

        return $this->canBeRendered($tag->getAssetName(), $tag->getType())
            ? $this->preRender($tag)
            : '';
    }

    /**
     * {@inheritdoc}
     */
    public function validate(TagInterface $tag)
    {
        return $tag instanceof RequireTagInterface && 0 === strpos($tag->getPath(), '@webpack/');
    }

    /**
     * Get the asset path.
     *
     * @param string $assetName The asset name
     * @param string $type      The asset type
     *
     * @return string
     */
    protected function getAssetPath($assetName, $type)
    {
        return $this->manager->getPath($assetName, $type);
    }

    /**
     * Prepare the render and do the render.
     *
     * @param RequireTagInterface $tag The require template tag
     *
     * @throws RequireTagRendererException When the asset in template tag is not managed by the Webpack Require Asset Manager
     *
     * @return string The output render
     */
    protected function preRender(RequireTagInterface $tag)
    {
        if ($this->isNonExistentOptionalTag($tag)) {
            return '';
        }

        $output = $this->doRender($tag, $tag->getPath());
        $output .= $this->preRenderLocalized($tag);

        return $output;
    }

    /**
     * Do render the HTML tag.
     *
     * @param RequireTagInterface $tag       The require template tag
     * @param string              $assetName The asset name
     *
     * @return string The output render
     */
    protected function doRender(RequireTagInterface $tag, $assetName)
    {
        $type = $tag->getType();
        $output = '';

        if ($this->canBeRendered($assetName, $type)) {
            $attributes = $this->prepareAttributes($tag, $assetName);
            $this->assetRendered($assetName, $type);

            $output = RequireUtil::renderHtmlTag($attributes, $tag->getHtmlTag(), $tag->shortEndTag());
        }

        return $output;
    }

    /**
     * Pre render the localized assets.
     *
     * @param RequireTagInterface $tag The require template tag
     *
     * @return string The output render
     */
    protected function preRenderLocalized(RequireTagInterface $tag)
    {
        $output = '';

        foreach ($this->getLocalizedAssets($tag->getPath()) as $localeAsset) {
            $output .= $this->doRender($tag, $localeAsset);
        }

        return $output;
    }

    /**
     * Prepare the attributes of HTML tag.
     *
     * @param RequireTagInterface $tag       The require template tag
     * @param string              $assetName The asset name
     *
     * @return array
     */
    protected function prepareAttributes(RequireTagInterface $tag, $assetName)
    {
        $path = $this->getAssetPath($assetName, $tag->getType());
        $attributes = $tag->getAttributes();
        $attributes[$tag->getLinkAttribute()] = $path;

        return $attributes;
    }

    /**
     * Check if the tag is a non existent optional require asset.
     *
     * @param RequireTagInterface $tag The require template tag
     *
     * @return bool
     */
    protected function isNonExistentOptionalTag(RequireTagInterface $tag)
    {
        if (!$this->manager->has($tag->getPath(), $tag->getType())) {
            if ($tag->isOptional()) {
                return true;
            }

            throw new RequireTagRendererException($tag, sprintf('The %s %s "%s" is not managed by the Webpack Require Asset Manager', $tag->getCategory(), $tag->getType(), $tag->getPath()));
        }

        return false;
    }

    /**
     * Indicate the asset is rendered.
     *
     * @param array|string $assets The asset name or list of asset name
     * @param string       $type   The require tag type
     */
    protected function assetRendered($assets, $type): void
    {
        $assets = (array) $assets;

        foreach ($assets as $asset) {
            $this->renderedTags[] = $type.'::'.$asset;
        }
    }
}
