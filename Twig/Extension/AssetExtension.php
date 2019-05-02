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

use Fxp\Component\RequireAsset\Asset\Config\AssetReplacementManagerInterface;
use Fxp\Component\RequireAsset\Exception\TagRendererExceptionInterface;
use Fxp\Component\RequireAsset\Exception\Twig\AlreadyExistTagPositionException;
use Fxp\Component\RequireAsset\Exception\Twig\MissingTagPositionException;
use Fxp\Component\RequireAsset\Exception\Twig\RequireTagException;
use Fxp\Component\RequireAsset\Exception\Twig\RuntimeTagRendererException;
use Fxp\Component\RequireAsset\Tag\Renderer\TagRendererInterface;
use Fxp\Component\RequireAsset\Tag\RequireTagInterface;
use Fxp\Component\RequireAsset\Tag\TagInterface;
use Fxp\Component\RequireAsset\Twig\TokenParser\InlineScriptTokenParser;
use Fxp\Component\RequireAsset\Twig\TokenParser\InlineStyleTokenParser;
use Fxp\Component\RequireAsset\Twig\TokenParser\RequireScriptTokenParser;
use Fxp\Component\RequireAsset\Twig\TokenParser\RequireStyleTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\Source;
use Twig\TwigFunction;

/**
 * AssetExtension extends Twig with global assets rendering capabilities.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetExtension extends AbstractExtension
{
    /**
     * @var null|AssetReplacementManagerInterface
     */
    protected $replacementManager;

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
     *
     * @param null|AssetReplacementManagerInterface $replacementManager The asset replacement manager
     */
    public function __construct(AssetReplacementManagerInterface $replacementManager = null)
    {
        $this->replacementManager = $replacementManager;
        $this->renderers = [];
        $this->contents = [];
        $this->tagPositions = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            $this->createTagPositionFunction('inlineScriptsPosition', ['category' => 'inline',  'type' => 'script']),
            $this->createTagPositionFunction('inlineStylesPosition', ['category' => 'inline',  'type' => 'style']),
            $this->createTagPositionFunction('requireScriptsPosition', ['category' => 'require',  'type' => 'script']),
            $this->createTagPositionFunction('requireStylesPosition', ['category' => 'require',  'type' => 'style']),
            new TwigFunction('renderAssetTags', [$this, 'renderTags'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [
            new InlineScriptTokenParser(\get_class($this)),
            new InlineStyleTokenParser(\get_class($this)),
            new RequireScriptTokenParser($this->replacementManager, \get_class($this)),
            new RequireStyleTokenParser($this->replacementManager, \get_class($this)),
        ];
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
        $this->renderers = [];

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
     * Create the tag position to included in the twig template.
     *
     * @param string      $category The twig asset category
     * @param string      $type     The asset type
     * @param int         $lineno   The lineno
     * @param null|string $name     The template logical name
     * @param null|string $position The name of tag position in the twig template
     *
     * @throws AlreadyExistTagPositionException When tag position is already defined in template
     *
     * @return string
     */
    public function createTagPosition($category, $type, $lineno = -1, $name = null, $position = null)
    {
        $tag = $this->formatTagPosition($category, $type, $position);

        if (\in_array($tag, $this->tagPositions, true)) {
            throw new AlreadyExistTagPositionException($category, $type, $position, $lineno, $name);
        }

        $this->tagPositions[] = $tag;

        return $this->getTagPosition($tag, $category);
    }

    /**
     * Render all template tags.
     *
     * @param bool $allPosition Check if all asset position must be rendered.
     *
     * Replaces the current buffer with the new edited buffer content
     *
     * @throws MissingTagPositionException
     * @throws RequireTagException
     * @throws RuntimeTagRendererException
     */
    public function renderTags($allPosition = true): void
    {
        $output = ob_get_contents();
        $start = 0;
        preg_match_all('/(<!--|\/\*)#tag-position:([\w0-9_:-]+):[\w0-9]+#(-->|\*\/)/', $output, $matches, PREG_OFFSET_CAPTURE);
        ob_clean();

        $this->renderContents($output, $matches, $start);
        $this->validateRenderTags($allPosition);
        $this->resetRenderers();
        $this->resetTagPosition();
    }

    /**
     * Reset tag positions and her contents.
     */
    public function resetTagPosition(): void
    {
        $this->contents = [];
        $this->tagPositions = [];
    }

    /**
     * @param string $output  The output
     * @param array  $matches The matches of tag position
     * @param int    $start   The start position for sub string
     *
     * @throws RequireTagException
     * @throws RuntimeTagRendererException
     */
    protected function renderContents($output, array $matches, &$start): void
    {
        foreach ($matches[0] as $i => $match) {
            $end = $match[1] - $start;
            $contentType = $matches[2][$i][0];
            echo substr($output, $start, $end);
            $this->doRenderTags($contentType);
            $start = $match[1] + \strlen($match[0]);
        }
        echo substr($output, $start);
    }

    /**
     * Do render the tags by type.
     *
     * @param string $contentType The content type
     *
     * @throws RequireTagException
     * @throws RuntimeTagRendererException
     */
    protected function doRenderTags($contentType): void
    {
        if (isset($this->contents[$contentType])) {
            $tags = $this->contents[$contentType];
            /** @var TagRendererInterface[] $renderers */
            $renderers = [];
            $rendererTags = [];

            foreach ($tags as $tag) {
                $renderer = $this->findRenderer($tag);
                $id = spl_object_hash($renderer);
                $renderers[$id] = $renderer;
                $rendererTags[$id][] = $tag;
            }

            foreach ($renderers as $id => $renderer) {
                foreach ($renderer->order($rendererTags[$id]) as $orderedTag) {
                    echo $this->renderTag($renderer, $orderedTag);
                }
            }
            unset($this->contents[$contentType]);
        }
    }

    /**
     * Find the template tag renderer.
     *
     * @param TagInterface $tag The template tag
     *
     * @throws RuntimeTagRendererException When no template tag renderer has been found
     *
     * @return TagRendererInterface
     */
    protected function findRenderer(TagInterface $tag)
    {
        foreach ($this->getRenderers() as $renderer) {
            if ($renderer->validate($tag)) {
                return $renderer;
            }
        }

        throw $this->buildRuntimeTagRendererException($tag);
    }

    /**
     * Build the runtime tag renderer exception.
     *
     * @param TagInterface $tag
     *
     * @return RuntimeTagRendererException
     */
    protected function buildRuntimeTagRendererException(TagInterface $tag)
    {
        $msg = sprintf('No template tag renderer has been found for the "%s_%s" tag', $tag->getCategory(), $tag->getType());

        if ($tag instanceof RequireTagInterface) {
            $msg .= sprintf(' with the asset "%s"', $tag->getPath());
        }

        return new RuntimeTagRendererException($msg);
    }

    /**
     * Render the tag.
     *
     * @param TagRendererInterface $renderer The template tag renderer
     * @param TagInterface         $tag      The template tag
     *
     * @throws RequireTagException
     *
     * @return string The rendered tag
     */
    protected function renderTag(TagRendererInterface $renderer, TagInterface $tag)
    {
        $content = '';

        try {
            $content .= $renderer->render($tag);
        } catch (TagRendererExceptionInterface $e) {
            $tag = $e->getTag();

            throw new RequireTagException($e->getMessage(), $tag->getTemplateLine(), $tag->getTemplateName() ? new Source('', $tag->getTemplateName()) : null, $e->getPrevious());
        }

        return $content;
    }

    /**
     * Validate the renderTags method.
     *
     * @param bool $allPosition Check if all asset position must be rendered
     *
     * @throws MissingTagPositionException When the tag positions are not injected in the template
     */
    protected function validateRenderTags($allPosition = true): void
    {
        if ($allPosition && !empty($this->contents)) {
            $keys = array_keys($this->contents);

            throw new MissingTagPositionException($this->contents[$keys[0]][0]);
        }
    }

    /**
     * Reset all renderers.
     */
    protected function resetRenderers(): void
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
     * @param null|string $position The name of template tag position in the template
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
            ? '/*'.'%s'.'*/'
            : '<!--%s-->';

        return sprintf($pattern, '#tag-position:'.$name.':'.spl_object_hash($this).'#');
    }

    /**
     * Create the tag position twig function.
     *
     * @param string $name    The name of function
     * @param array  $options The options of function
     *
     * @return TwigFunction
     */
    private function createTagPositionFunction($name, array $options)
    {
        $options = array_merge($options, [
            'node_class' => 'Fxp\Component\RequireAsset\Twig\Node\TagPositionFunctionNode',
            'is_safe' => ['html'],
            'category' => null,
            'type' => null,
        ], $options);
        $callable = [$this, 'createTagPosition'];

        $tagPosition = new TwigFunction($name, $callable, $options);
        $tagPosition->setArguments([
            $options['category'],
            $options['type'],
        ]);

        return $tagPosition;
    }
}
