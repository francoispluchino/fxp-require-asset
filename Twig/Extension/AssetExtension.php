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

use Fxp\Component\RequireAsset\Exception\TagRendererExceptionInterface;
use Fxp\Component\RequireAsset\Exception\Twig\AlreadyExistTagPositionException;
use Fxp\Component\RequireAsset\Exception\Twig\RuntimeTagRendererException;
use Fxp\Component\RequireAsset\Exception\Twig\MissingTagPositionException;
use Fxp\Component\RequireAsset\Exception\Twig\RequireTagException;
use Fxp\Component\RequireAsset\Tag\TagInterface;
use Fxp\Component\RequireAsset\Tag\Renderer\TagRendererInterface;
use Fxp\Component\RequireAsset\Twig\TokenParser\InlineScriptTokenParser;
use Fxp\Component\RequireAsset\Twig\TokenParser\InlineStyleTokenParser;
use Fxp\Component\RequireAsset\Twig\TokenParser\RequireScriptTokenParser;
use Fxp\Component\RequireAsset\Twig\TokenParser\RequireStyleTokenParser;
use Fxp\Component\RequireAsset\Twig\TwigFunction\TagPositionFunction;

/**
 * AssetExtension extends Twig with global assets rendering capabilities.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetExtension extends \Twig_Extension
{
    /**
     * @var TagRendererInterface[]
     */
    protected $renderers;

    /**
     * @var array
     */
    protected $contents;

    /**
     * @var array
     */
    protected $tagPositions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->renderers = array();
        $this->contents = array();
        $this->tagPositions = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new TagPositionFunction('inlineScriptsPosition',  array($this, 'createTagPosition'), array('category' => 'inline',  'type' => 'script')),
            new TagPositionFunction('inlineStylesPosition',   array($this, 'createTagPosition'), array('category' => 'inline',  'type' => 'style')),
            new TagPositionFunction('requireScriptsPosition', array($this, 'createTagPosition'), array('category' => 'require', 'type' => 'script')),
            new TagPositionFunction('requireStylesPosition',  array($this, 'createTagPosition'), array('category' => 'require', 'type' => 'style')),
            new \Twig_SimpleFunction('renderAssetTags',       array($this, 'renderTags'),        array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        $tokens = array(
            new InlineScriptTokenParser(),
            new InlineStyleTokenParser(),
            new RequireScriptTokenParser(),
            new RequireStyleTokenParser(),
        );

        return $tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fxp_require_asset';
    }

    /**
     * Add template tag renderer.
     *
     * @param TagRendererInterface $renderer The template tag renderer
     *
     * @return self
     */
    public function addRenderer(TagRendererInterface $renderer)
    {
        $this->renderers[] = $renderer;

        return $this;
    }

    /**
     * Set the template tag renderers.
     *
     * @param TagRendererInterface[] $renderers The template tag renderers
     *
     * @return self
     */
    public function setRenderers(array $renderers)
    {
        $this->renderers = array();

        foreach ($renderers as $renderer) {
            $this->addRenderer($renderer);
        }

        return $this;
    }

    /**
     * Get the template tag renderers.
     *
     * @return TagRendererInterface[]
     */
    public function getRenderers()
    {
        return $this->renderers;
    }

    /**
     * Add template tag.
     *
     * @param TagInterface $tag
     *
     * @return self
     */
    public function addTag(TagInterface $tag)
    {
        $this->contents[$tag->getTagPositionName()][] = $tag;

        return $this;
    }

    /**
     * Create the tagposition to included in the twig template.
     *
     * @param string      $category The twig asset category
     * @param string      $type     The asset type
     * @param int         $lineno   The lineno
     * @param string|null $filename The twig filename
     * @param string|null $position The name of tag position in the twig template
     *
     * @return string
     *
     * @throws AlreadyExistTagPositionException When tag position is already defined in template
     */
    public function createTagPosition($category, $type, $lineno = -1, $filename = null, $position = null)
    {
        $pos = trim($position, '_');
        $pos = strlen($pos) > 0 ? '_' . $pos : '';
        $tag = strtoupper($category . '_' . $type . $pos);

        if (isset($this->tagPositions[$tag])) {
            throw new AlreadyExistTagPositionException($category, $type, $position, $lineno, $filename);
        }

        $this->tagPositions[$tag] = $this->formatTagPosition($category, $type, $position);

        return $this->getTagPosition($tag);
    }

    /**
     * Render all template tags.
     *
     * Replaces the current buffer with the new edited buffer content.
     */
    public function renderTags()
    {
        $output = ob_get_contents();

        foreach ($this->tagPositions as $name => $contentType) {
            $output = $this->doRenderTags($name, $contentType, $output);
        }

        ob_clean();
        echo $output;

        $this->validateRenderTags();
        $this->resetRenderers();
    }

    /**
     * Do render the tags by type.
     *
     * @param string $name
     * @param string $contentType
     * @param string $output
     *
     * @return string The output with replaced asset tag position
     */
    protected function doRenderTags($name, $contentType, $output)
    {
        $content = '';

        if (isset($this->contents[$contentType])) {
            $renderer = null;
            foreach ($this->contents[$contentType] as $tag) {
                if (null === $renderer) {
                    $renderer = $this->findRenderer($tag);
                }
                $content .= $this->renderTag($renderer, $tag);
            }
            unset($this->contents[$contentType]);
        }

        return str_replace($this->getTagPosition($name), $content, $output);
    }

    /**
     * Find the template tag renderer.
     *
     * @param TagInterface $tag The template tag
     *
     * @return TagRendererInterface
     *
     * @throws RuntimeTagRendererException When no template tag renderer has been found
     */
    protected function findRenderer(TagInterface $tag)
    {
        foreach ($this->getRenderers() as $renderer) {
            if ($renderer->validate($tag)) {
                return $renderer;
            }
        }

        throw new RuntimeTagRendererException(sprintf('No template tag renderer has been found for the "%s_%s" tag', $tag->getCategory(), $tag->getType()), $tag->getLineno(), $tag->getFilename());
    }

    /**
     * Render the tag.
     *
     * @param TagRendererInterface $renderer The template tag renderer
     * @param TagInterface         $tag      The template tag
     *
     * @return string The rendered tag
     *
     * @throws RequireTagException
     */
    protected function renderTag(TagRendererInterface $renderer, TagInterface $tag)
    {
        $content = '';

        try {
            $content .= $renderer->render($tag);
        } catch (TagRendererExceptionInterface $e) {
            $tag = $e->getTag();
            throw new RequireTagException($e->getMessage(), $tag->getLineno(), $tag->getFilename(), $e->getPrevious());
        }

        return $content;
    }

    /**
     * Validate the renderTags method.
     *
     * @throws MissingTagPositionException When the tag positions are not injected in the template
     */
    protected function validateRenderTags()
    {
        if (!empty($this->contents)) {
            $keys = array_keys($this->contents);

            throw new MissingTagPositionException($this->contents[$keys[0]][0]);
        }
    }

    /**
     * Reset all renderers.
     *
     * @return void
     */
    protected function resetRenderers()
    {
        foreach ($this->getRenderers() as $renderer) {
            $renderer->reset();
        }
    }

    /**
     * Format the template tag position.
     *
     * @param string      $category The template tag category
     * @param string      $type     The template tag type
     * @param string|null $position The name of template tag position in the template
     *
     * @return string The formatted tag position
     */
    protected function formatTagPosition($category, $type, $position = null)
    {
        return strtolower($category . ':' . $type . ':' . $position);
    }

    /**
     * Get the tag position of inline tag.
     *
     * @param string $name The tag position name
     *
     * @return string The tag position for the template
     */
    protected function getTagPosition($name)
    {
        return '{#TAG_POSITION_' . $name . '_'.spl_object_hash($this).'#}';
    }
}
