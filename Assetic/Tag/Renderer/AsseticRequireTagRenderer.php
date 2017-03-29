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

use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetReference;
use Fxp\Component\RequireAsset\Asset\Util\AssetUtils;
use Fxp\Component\RequireAsset\Exception\RequireTagRendererException;
use Fxp\Component\RequireAsset\Tag\RequireTagInterface;
use Fxp\Component\RequireAsset\Tag\TagInterface;

/**
 * Template assetic require tag renderer.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AsseticRequireTagRenderer extends AbstractAsseticRequireTagRenderer
{
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

        return $this->canBeRendered($tag->getAssetName())
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
     * Configure the common template tag.
     *
     * @param RequireTagInterface $tag The template tag
     *
     * @return bool Return TRUE if the tag is a common asset
     */
    protected function configureCommonTag(RequireTagInterface $tag)
    {
        if ($this->manager->hasFormula($tag->getAssetName())) {
            $resource = $this->manager->getFormula($tag->getAssetName());

            if (isset($resource[2]['fxp_require_common_asset']) && $resource[2]['fxp_require_common_asset']) {
                $tag->setInputs($resource[0]);

                return true;
            }
        }

        return false;
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
        if ($this->isNonExistentOptionalTag($tag)) {
            return '';
        }

        if (isset($this->debugCommonAssets[$tag->getAssetName()])) {
            $tag->setInputs($this->debugCommonAssets[$tag->getAssetName()]);
        }

        if ($this->manager->isDebug() && count($tag->getInputs()) > 0) {
            return $this->preRenderCommonDebug($tag);
        }

        return $this->preRenderProd($tag);
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
        if (!$this->manager->has($tag->getAssetName())) {
            if ($tag->isOptional()) {
                return true;
            } elseif (!isset($this->debugCommonAssets[$tag->getAssetName()])) {
                throw new RequireTagRendererException($tag, sprintf('The %s %s "%s" is not managed by the Assetic Manager', $tag->getCategory(), $tag->getType(), $tag->getPath()));
            }
        }

        return false;
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

        foreach ($this->getLocalizedAssets($tag->getPath()) as $localeAsset) {
            $output .= $this->doRenderCommonDebug($tag, AssetUtils::formatName($localeAsset));
        }

        return $output.$this->includeMissingLocalizedAssets($tag);
    }

    /**
     * Do the render of common assets in debug mode and do the render.
     *
     * @param RequireTagInterface $tag       The require template tag
     * @param string|null         $assetName The asset name
     *
     * @return string The output render
     */
    protected function doRenderCommonDebug(RequireTagInterface $tag, $assetName = null)
    {
        $assetName = null !== $assetName ? $assetName : $tag->getAssetName();
        $output = '';

        if (!$this->manager->has($assetName)) {
            foreach ($tag->getInputs() as $input) {
                $output .= $this->doRender($tag, AssetUtils::formatName($input));
            }
        } else {
            /* @var AssetCollection $asset */
            $asset = $this->manager->get($assetName);
            $iterator = $asset->getIterator();

            /* @var AssetReference $child */
            foreach ($iterator as $child) {
                $output .= $this->doRender($tag, $this->extractAsseticName($child), $child);
            }
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
        $output = $this->doRender($tag, $tag->getAssetName());
        $this->assetRendered($tag->getInputs());

        $output .= $this->preRenderLocalized($tag, $tag->getPath());
        foreach ($tag->getInputs() as $input) {
            $this->assetRendered($this->getLocalizedAssets($input));
        }

        $output .= $this->includeMissingLocalizedAssets($tag, !$this->manager->isDebug());

        return $output;
    }

    /**
     * Render the individual localized asset if it is not present in the common localized asset.
     *
     * @param RequireTagInterface $tag     The require template tag
     * @param bool                $analyse Check if analyse is required
     *
     * @return string The output render
     */
    protected function includeMissingLocalizedAssets(RequireTagInterface $tag, $analyse = true)
    {
        $output = '';

        if ($analyse) {
            foreach ($tag->getInputs() as $input) {
                $output .= $this->preRenderLocalized($tag, $input);
            }
        }

        return $output;
    }
}
