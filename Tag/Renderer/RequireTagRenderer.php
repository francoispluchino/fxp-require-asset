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

use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetInterface;
use Assetic\Factory\LazyAssetManager;
use Assetic\Util\VarUtils;
use Fxp\Component\RequireAsset\Assetic\RequireLocaleManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Util\Utils;
use Fxp\Component\RequireAsset\Exception\RequireTagRendererException;
use Fxp\Component\RequireAsset\Tag\TagInterface;
use Fxp\Component\RequireAsset\Tag\RequireTagInterface;

/**
 * Template require tag renderer.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireTagRenderer implements TagRendererInterface
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
    public function order(array $tags)
    {
        $commons = array();
        $singles = array();

        /* @var RequireTagInterface $tag */
        foreach ($tags as $tag) {
            if ($this->configureCommonTag($tag)) {
                $commons[] = $tag;
            } else {
                $singles[] = $tag;
            }
        }

        return array_merge($commons, $singles);
    }

    /**
     * {@inheritdoc}
     */
    public function render(TagInterface $tag)
    {
        /* @var RequireTagInterface $tag */

        return $this->canBeRendered($tag)
            ? $this->preRender($tag)
            : '';
    }

    /**
     * {@inheritdoc}
     */
    public function validate(TagInterface $tag)
    {
        return $tag instanceof RequireTagInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->renderedTags = array();
    }

    /**
     * Configure the common template tag.
     *
     * @param RequireTagInterface $tag The template tag
     *
     * @return bool Return TRUE if the tag is a common asset
     */
    protected function configureCommonTag(RequireTagInterface $tag)
    {
        if ($this->manager->hasFormula($tag->getAsseticName())) {
            $resource = $this->manager->getFormula($tag->getAsseticName());

            if (isset($resource[2]['fxp_require_common_asset']) && $resource[2]['fxp_require_common_asset']) {
                $tag->setInputs($resource[0]);

                return true;
            }
        }

        return false;
    }

    /**
     * Check if the template tag can be rendered.
     *
     * @param RequireTagInterface $tag
     *
     * @return bool
     */
    protected function canBeRendered(RequireTagInterface $tag)
    {
        if (in_array($tag->getAsseticName(), $this->renderedTags)) {
            return false;
        }

        $this->renderedTags[] = $tag->getAsseticName();

        foreach ($tag->getInputs() as $input) {
            $this->renderedTags[] = Utils::formatName($input);
        }

        return true;
    }

    /**
     * Prepare the render and do the render.
     *
     * @param RequireTagInterface $tag The require template tag
     *
     * @return string The output render
     *
     * @throws RequireTagRendererException When the asset in template tag is not managed by the Assetic Manager
     */
    protected function preRender(RequireTagInterface $tag)
    {
        if (!$this->manager->has($tag->getAsseticName())) {
            throw new RequireTagRendererException($tag, sprintf('The %s %s "%s" is not managed by the Assetic Manager', $tag->getCategory(), $tag->getType(), $tag->getPath()));
        }

        if ($this->manager->isDebug() && count($tag->getInputs()) > 0) {
            return $this->preRenderCommonDebug($tag);
        }

        return $this->preRenderProd($tag);
    }

    /**
     * Prepare the render of common assets in debug mode and do the render.
     *
     * @param RequireTagInterface $tag The require template tag
     *
     * @return string The output render
     */
    protected function preRenderCommonDebug(RequireTagInterface $tag)
    {
        $output = $this->doRenderCommonDebug($tag);

        foreach ($this->getLocalizedAssets($tag) as $localeAsset) {
            $output .= $this->doRenderCommonDebug($tag, Utils::formatName($localeAsset));
        }

        return $output;
    }

    /**
     * Do the render of common assets in debug mode and do the render.
     *
     * @param RequireTagInterface $tag         The require template tag
     * @param string|null         $asseticName The assetic name
     *
     * @return string The output render
     */
    protected function doRenderCommonDebug(RequireTagInterface $tag, $asseticName = null)
    {
        /* @var AssetCollection $asset */
        $asseticName = null !== $asseticName ? $asseticName : $tag->getAsseticName();
        $asset = $this->manager->get($asseticName);
        $iterator = $asset->getIterator();
        $output = '';

        foreach ($iterator as $child) {
            $target = $this->getTargetPath($child);
            $attributes = $tag->getAttributes();
            $attributes[$tag->getLinkAttribute()] = $target;

            $output .= RequireUtil::doRender($attributes, $tag->getHtmlTag(), $tag->shortEndTag());
        }

        return $output;
    }

    /**
     * Prepare the render of asset and do the render.
     *
     * @param RequireTagInterface $tag The require template tag
     *
     * @return string The output render
     */
    protected function preRenderProd(RequireTagInterface $tag)
    {
        $attributes = $this->prepareAttributes($tag, $tag->getAsseticName());
        $output = RequireUtil::doRender($attributes, $tag->getHtmlTag(), $tag->shortEndTag());

        foreach ($this->getLocalizedAssets($tag) as $localeAsset) {
            $localeAttrs = $this->prepareAttributes($tag, Utils::formatName($localeAsset));
            $output .= RequireUtil::doRender($localeAttrs, $tag->getHtmlTag(), $tag->shortEndTag());
        }

        return $output;
    }

    /**
     * @param RequireTagInterface $tag         The require tag
     * @param string              $asseticName The assetic name of asset
     *
     * @return array
     */
    protected function prepareAttributes(RequireTagInterface $tag, $asseticName)
    {
        $asset = $this->manager->get($asseticName);
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
     * @param RequireTagInterface $tag The require tag
     *
     * @return string[]
     */
    protected function getLocalizedAssets(RequireTagInterface $tag)
    {
        $locales = array();

        if (null !== $this->localeManager) {
            $locales = $this->localeManager->getLocalizedAsset($tag->getPath());
        }

        return $locales;
    }
}
