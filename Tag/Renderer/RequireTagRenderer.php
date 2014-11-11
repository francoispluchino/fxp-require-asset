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
use Assetic\AssetManager;
use Assetic\Util\VarUtils;
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

        $asset = $this->manager->get($tag->getAsseticName());
        $target = $this->getTargetPath($asset);
        $attributes = $tag->getAttributes();
        $attributes[$tag->getLinkAttribute()] = $target;

        return $this->doRender($attributes, $tag->getHtmlTag(), $tag->shortEndTag());
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
     * Do render.
     *
     * @param array  $attributes  The HTML attributes
     * @param string $htmlTag     The HTML tag name
     * @param bool   $shortEndTag Check if the end HTML tag must be in a short or long format
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

        $output .= $shortEndTag ? ' />' : '></' . $htmlTag . '>';

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