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

use Fxp\Component\RequireAsset\Twig\TokenParser\InlineAssetTokenParser;

/**
 * InlineAssetExtension extends Twig with global assets rendering capabilities.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InlineAssetExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    protected $javascripts;

    /**
     * @var array
     */
    protected $stylesheets;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->javascripts = array();
        $this->stylesheets = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('inlineJavascriptsPosition', array($this, 'inlineJavascriptsPosition'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('inlineStylesheetsPosition', array($this, 'inlineStylesheetsPosition'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('renderInlineAssets',        array($this, 'renderInlineAssets'),        array('is_safe' => array('html'))),
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
        return 'fxp_require_asset_inline_asset';
    }

    /**
     * Adds inline asset.
     *
     * @param string $type
     * @param array  $callable
     * @param array  $context
     * @param array  $blocks
     *
     * @throws \Twig_Error_Runtime When type is not javascript or stylesheet
     */
    public function addAsset($type, $callable, array $context, array $blocks)
    {
        $asset = array(
            'callable' => $callable,
            'context'  => $context,
            'blocks'   => $blocks,
        );

        if ('javascript' === $type) {
            $this->javascripts[] = $asset;

        } elseif ('stylesheet' === $type) {
            $this->stylesheets[] = $asset;

        } else {
            throw new \Twig_Error_Runtime('The asset type must be only "javascript" or "stylesheet"');
        }
    }

    /**
     * Tag the inline javascripts position.
     *
     * @return string
     */
    public function inlineJavascriptsPosition()
    {
        return $this->getTagPosition('javascripts');
    }

    /**
     * Tag the inline stylesheets position.
     *
     * @return string
     */
    public function inlineStylesheetsPosition()
    {
        return $this->getTagPosition('stylesheets');
    }

    /**
     * Render inline assets.
     *
     * Replaces the current buffer with the new edited buffer content.
     */
    public function renderInlineAssets()
    {
        $output = ob_get_contents();

        $output = str_replace($this->inlineJavascriptsPosition(), $this->doRenderInlineAssets('javascripts'), $output);
        $output = str_replace($this->inlineStylesheetsPosition(), $this->doRenderInlineAssets('stylesheets'), $output);

        ob_clean();
        echo $output;
    }

    /**
     * Execution of render all global inline assets.
     *
     * @param string $type The asset type
     *
     * @return string
     */
    protected function doRenderInlineAssets($type)
    {
        $output = '';

        foreach ($this->$type as $asset) {
            $output .= $this->renderInlineAsset($asset['callable'], $asset['context'], $asset['blocks']);
        }

        $this->$type = array();

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
     * @throws \Twig_Error_Runtime When the callable argument is not an array with Twig_Tempate instance of the block
     */
    protected function renderInlineAsset(array $callable, array $context, array $blocks)
    {
        if (2 !== count($callable) || !$callable[0] instanceof \Twig_Template || !is_string($callable[1])) {
            throw new \Twig_Error_Runtime('The callable argument must be an array with Twig_Template instance and name function of the block to rendering');
        }

        return $callable[0]->renderBlock($callable[1], $context, $blocks);
    }

    /**
     * Gets the tag position of inline asset.
     *
     * @param string $type The asset type
     *
     * @return string The tag position
     */
    protected function getTagPosition($type)
    {
        return '{#TAG_POSITION_EMBED_' . strtoupper($type) . '_'.spl_object_hash($this).'#}';
    }
}
