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
        $tag = $this->formatTagPosition($category, $type, $position);

        if (in_array($tag, $this->tagPositions)) {
            throw new AlreadyExistTagPositionException($category, $type, $position, $lineno, $filename);
        }

        $this->tagPositions[] = $tag;

        return $this->getTagPosition($tag, $category);
    }

    /**
     * Render all template tags.
     *
     * Replaces the current buffer with the new edited buffer content.
     */
    public function renderTags()
    {
        $output = ob_get_contents();
        $start = 0;
        preg_match_all('/(<!--|\/\*)#tag-position:([a-z_:]+):[\w0-9]+#(-->|\*\/)/', $output, $matches, PREG_OFFSET_CAPTURE);
        ob_clean();

        foreach ($matches[0] as $i => $match) {
            $end = $match[1] - $start;
            $contentType = $matches[2][$i][0];
            echo substr($output, $start, $end).$this->doRenderTags($contentType);
            $start = $match[1] + strlen($match[0]);
        }
        echo substr($output, $start);

        $this->validateRenderTags();
        $this->resetRenderers();
    }

    /**
     * Do render the tags by type.
     *
     * @param string $contentType
     *
     * @return string The full content of tag position
     */
    protected function doRenderTags($contentType)
    {
        $content = '';

        if (isset($this->contents[$contentType])) {
            $renderer = $this->findRenderer(current($this->contents[$contentType]));
            foreach ($renderer->order($this->contents[$contentType]) as $tag) {
                $content .= $this->renderTag($renderer, $tag);
            }
            unset($this->contents[$contentType]);
        }

        return $content;
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
        return strtolower($category.':'.$type.':'.$position);
    }

    /**
     * Get the tag position of inline tag.
     *
     * @param string $name     The tag position name
     * @param string $category The tag category
     *
     * @return string The tag position for the template
     */
    protected function getTagPosition($name, $category)
    {
        $pattern = 'inline' === $category
            ? '/*%s*/'
            : '<!--%s-->';

        return sprintf($pattern, '#tag-position:'.$name.':'.spl_object_hash($this).'#');
    }
}
