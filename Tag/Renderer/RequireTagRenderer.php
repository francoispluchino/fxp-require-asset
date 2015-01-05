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
use Assetic\Asset\AssetReference;
use Fxp\Component\RequireAsset\Assetic\Util\Utils;
use Fxp\Component\RequireAsset\Exception\RequireTagRendererException;
use Fxp\Component\RequireAsset\Tag\TagInterface;
use Fxp\Component\RequireAsset\Tag\RequireTagInterface;

/**
 * Template require tag renderer.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireTagRenderer extends AbstractRequireTagRenderer
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

        return $this->canBeRendered($tag->getAsseticName())
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
            if ($tag->isOptional()) {
                return '';
            }

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

        foreach ($this->getLocalizedAssets($tag->getPath()) as $localeAsset) {
            $output .= $this->doRenderCommonDebug($tag, Utils::formatName($localeAsset));
        }

        return $output.$this->includeMissingLocalizedAssets($tag);
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
        $asseticName = null !== $asseticName ? $asseticName : $tag->getAsseticName();
        /* @var AssetCollection $asset */
        $asset = $this->manager->get($asseticName);
        $iterator = $asset->getIterator();
        $output = '';

        /* @var AssetReference $child */
        foreach ($iterator as $child) {
            $output .= $this->doRender($tag, $this->extractAsseticName($child), $child);
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
        $output = $this->doRender($tag, $tag->getAsseticName());
        $output .= $this->preRenderLocalized($tag, $tag->getPath());
        $output .= $this->includeMissingLocalizedAssets($tag, !$this->manager->isDebug());

        $this->assetRendered($tag->getInputs());

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
