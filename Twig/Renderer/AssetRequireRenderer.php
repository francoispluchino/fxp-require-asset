<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\Renderer;

use Assetic\Asset\AssetInterface;
use Assetic\AssetManager;
use Assetic\Util\VarUtils;
use Fxp\Component\RequireAsset\Exception\Twig\RequireAssetException;
use Fxp\Component\RequireAsset\Twig\Asset\TwigAssetInterface;
use Fxp\Component\RequireAsset\Twig\Asset\TwigRequireAssetInterface;

/**
 * Asset require renderer.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetRequireRenderer implements AssetRendererInterface
{
    /**
     * The list of already rendered assets.
     * @var array
     */
    protected $renderedAssets;

    /**
     * @var AssetManager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param AssetManager $manager
     */
    public function __construct(AssetManager $manager)
    {
        $this->manager = $manager;
        $this->reset();
    }

    /**
     * {@inheritdoc}
     */
    public function render(TwigAssetInterface $asset)
    {
        /* @var TwigRequireAssetInterface $asset */

        return $this->canBeRendered($asset)
            ? $this->preRender($asset)
            : '';
    }

    /**
     * {@inheritdoc}
     */
    public function validate(TwigAssetInterface $asset)
    {
        return $asset instanceof TwigRequireAssetInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->renderedAssets = array();
    }

    /**
     * Check if the asset can be rendered.
     *
     * @param TwigRequireAssetInterface $asset
     *
     * @return bool
     */
    protected function canBeRendered(TwigRequireAssetInterface $asset)
    {
        if (in_array($asset->getAsseticName(), $this->renderedAssets)) {
            return false;
        }

        $this->renderedAssets[] = $asset->getAsseticName();

        return true;
    }

    /**
     * Prepare the render and do the render.
     *
     * @param TwigRequireAssetInterface $asset The twig require asset
     *
     * @return string The output render
     *
     * @throws RequireAssetException When the asset is not managed by the Assetic Manager
     */
    protected function preRender(TwigRequireAssetInterface $asset)
    {
        if (!$this->manager->has($asset->getAsseticName())) {
            throw new RequireAssetException(sprintf('The %s %s "%s" is not managed by the Assetic Manager', $asset->getCategory(), $asset->getType(), $asset->getPath()), $asset->getLineno(), $asset->getFilename());
        }

        $asseticAsset = $this->manager->get($asset->getAsseticName());
        $target = $this->getTargetPath($asseticAsset);
        $attributes = $asset->getAttributes();
        $attributes[$asset->getLinkAttribute()] = $target;

        return $this->doRender($attributes, $asset->getHtmlTag(), $asset->shortEndTag());
    }

    /**
     * Get the target path of the asset.
     *
     * @param AssetInterface $asseticAsset The assetic asset
     *
     * @return string
     */
    protected function getTargetPath(AssetInterface $asseticAsset)
    {
        $target = str_replace('_controller/', '', $asseticAsset->getTargetPath());
        $target = VarUtils::resolve($target, $asseticAsset->getVars(), $asseticAsset->getValues());

        return $target;
    }

    /**
     * Do render.
     *
     * @param array  $attributes  The HTML attributes
     * @param string $htmlTag     The HTML tag name
     * @param bool   $shortEndTag Check if the end tag must be in a short or long format
     *
     * @return string The output render
     */
    protected function doRender(array $attributes, $htmlTag, $shortEndTag)
    {
        $output = '<' . $htmlTag;

        foreach ($attributes as $attr => $value) {
            if ($this->isValidValue($value)) {
                $output .= ' ' . $attr . '="' . $value . '"';
            }
        }

        if ($shortEndTag) {
            $output .= ' />';
        } else {
            $output .= '></' . $htmlTag . '>';
        }

        return $output;
    }

    /**
     * Check if the value of attribute can be added in the render.
     *
     * @param mixed $value The attribute value
     *
     * @return bool
     */
    protected function isValidValue($value)
    {
        return !empty($value) && is_scalar($value) && !is_bool($value);
    }
}
