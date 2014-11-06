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

use Fxp\Component\RequireAsset\Exception\InvalidTwigArgumentException;
use Fxp\Component\RequireAsset\Exception\InvalidTwigConfigurationException;
use Fxp\Component\RequireAsset\Exception\TwigRuntimeException;
use Fxp\Component\RequireAsset\Twig\TokenParser\InlineAssetTokenParser;

/**
 * AssetExtension extends Twig with global assets rendering capabilities.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetExtension extends \Twig_Extension
{
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
        $this->contents = array();
        $this->tagPositions = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('inlineJavascriptsPosition', array($this, 'inlineJavascriptsPosition'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('inlineStylesheetsPosition', array($this, 'inlineStylesheetsPosition'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('renderAssets',              array($this, 'renderAssets'),              array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        $tokens = array(
            new InlineAssetTokenParser('javascript'),
            new InlineAssetTokenParser('stylesheet'),
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
     * Adds inline asset.
     *
     * @param string $type
     * @param array  $callable
     * @param array  $context
     * @param array  $blocks
     * @param string $position
     *
     * @throws TwigRuntimeException When type is not javascript or stylesheet
     */
    public function addInlineAsset($type, $callable, array $context, array $blocks, $position = null)
    {
        if (!in_array($type, array('javascript', 'stylesheet'))) {
            throw new TwigRuntimeException('The asset type must be only "javascript" or "stylesheet"');
        }

        $this->contents[$this->formatTagPosition('inline', $type, $position)][] = array(
            'callable' => $callable,
            'context'  => $context,
            'blocks'   => $blocks,
        );
    }

    /**
     * Tag the inline javascripts position.
     *
     * @param string|null $position The name of tag position in the twig template
     *
     * @return string
     */
    public function inlineJavascriptsPosition($position = null)
    {
        return $this->addTagPosition('inline', 'javascript', $position);
    }

    /**
     * Tag the inline stylesheets position.
     *
     * @param string|null $position The name of tag position in the twig template
     *
     * @return string
     */
    public function inlineStylesheetsPosition($position = null)
    {
        return $this->addTagPosition('inline', 'stylesheet', $position);
    }

    /**
     * Render all assets.
     *
     * Replaces the current buffer with the new edited buffer content.
     */
    public function renderAssets()
    {
        $output = ob_get_contents();

        foreach ($this->tagPositions as $name => $contentType) {
            $output = $this->doRenderAssets($name, $contentType, $output);
        }

        ob_clean();
        echo $output;

        $this->validateRenderAssets();
    }

    /**
     * Do render the assets by type.
     *
     * @param string $name
     * @param string $contentType
     * @param string $output
     *
     * @return string The output with replaced asset tag position
     */
    protected function doRenderAssets($name, $contentType, $output)
    {
        $content = '';

        if (isset($this->contents[$contentType])) {
            if (0 === strpos($contentType, 'inline:')) {
                $content = $this->doRenderInlineAssets($this->contents[$contentType]);
            }

            unset($this->contents[$contentType]);
        }

        return str_replace($this->getTagPosition($name), $content, $output);
    }

    /**
     * Execution of render all global assets.
     *
     * @param array $assets The assets
     *
     * @return string
     */
    protected function doRenderInlineAssets(array $assets)
    {
        $output = '';

        foreach ($assets as $asset) {
            $output .= $this->renderInlineAsset($asset['callable'], $asset['context'], $asset['blocks']);
        }

        return $output;
    }

    /**
     * Render inline asset.
     *
     * @param array $callable
     * @param array $context
     * @param array $blocks
     *
     * @return string
     *
     * @throws TwigRuntimeException When the callable argument is not an array with Twig_Tempate instance of the block
     */
    protected function renderInlineAsset(array $callable, array $context, array $blocks)
    {
        if (2 !== count($callable) || !$callable[0] instanceof \Twig_Template || !is_string($callable[1])) {
            throw new TwigRuntimeException('The callable argument must be an array with Twig_Template instance and name function of the block to rendering');
        }

        return $callable[0]->renderBlock($callable[1], $context, $blocks);
    }

    /**
     * Validate the renderAssets method.
     *
     * @throws InvalidTwigConfigurationException When the contents assets are not injected in the template
     */
    protected function validateRenderAssets()
    {
        if (empty($this->contents)) {
            return;
        }

        $keys = array_keys($this->contents);
        list($name, $type, $position) = explode(':', $keys[0]);

        throw new InvalidTwigConfigurationException($name, $type, $position);
    }

    /**
     * Add the tag position of asset.
     *
     * @param string      $name     The tag position name
     * @param string      $type     The asset type
     * @param string|null $position The name of tag position in the twig template
     *
     * @return string The tag position
     *
     * @throws InvalidTwigArgumentException When tag position is already defined in template
     */
    protected function addTagPosition($name, $type, $position = null)
    {
        $pos = trim($position, '_');
        $pos = strlen($pos) > 0 ? '_' . $pos : '';
        $tag = strtoupper($name . '_' . $type . $pos);

        if (isset($this->tagPositions[$tag])) {
            throw new InvalidTwigArgumentException($name, $type, $position);
        }

        $this->tagPositions[$tag] = $this->formatTagPosition($name, $type, $position);

        return $this->getTagPosition($tag);
    }

    /**
     * Format the tag position of asset.
     *
     * @param string      $name     The tag position name
     * @param string      $type     The asset type
     * @param string|null $position The name of tag position in the twig template
     *
     * @return string The formatted tag position
     */
    protected function formatTagPosition($name, $type, $position = null)
    {
        return strtolower($name . ':' . $type . ':' . $position);
    }

    /**
     * Get the tag position of inline asset.
     *
     * @param string $name The tag position name
     *
     * @return string The tag position
     */
    protected function getTagPosition($name)
    {
        return '{#TAG_POSITION_' . $name . '_'.spl_object_hash($this).'#}';
    }
}
