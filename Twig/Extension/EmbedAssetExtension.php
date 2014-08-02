<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Twig\Extension;

use Fxp\Bundle\RequireAssetBundle\Twig\TokenParser\EmbedAssetTokenParser;

/**
 * EmbedAssetExtension extends Twig with global assets rendering capabilities.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class EmbedAssetExtension extends \Twig_Extension
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
            new \Twig_SimpleFunction('embedJavascriptsPosition', array($this, 'embedJavascriptsPosition'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('embedStylesheetsPosition', array($this, 'embedStylesheetsPosition'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('renderEmbedAssets',        array($this, 'renderEmbedAssets'),        array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        $tokens = array(
            new EmbedAssetTokenParser('javascript'),
            new EmbedAssetTokenParser('stylesheet'),
        );

        return $tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fxp_require_asset_embed_asset';
    }

    /**
     * Adds embed asset.
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
     * Tag the embed javascripts position.
     *
     * @return string
     */
    public function embedJavascriptsPosition()
    {
        return $this->getTagPosition('javascripts');
    }

    /**
     * Tag the embed stylesheets position.
     *
     * @return string
     */
    public function embedStylesheetsPosition()
    {
        return $this->getTagPosition('stylesheets');
    }

    /**
     * Render embed assets.
     *
     * Replaces the current buffer with the new edited buffer content.
     */
    public function renderEmbedAssets()
    {
        $output = ob_get_contents();

        $output = str_replace($this->embedJavascriptsPosition(), $this->renderEmbedJavascripts(), $output);
        $output = str_replace($this->embedStylesheetsPosition(), $this->renderEmbedStylesheets(), $output);

        ob_clean();
        echo $output;
    }

    /**
     * Render all global embed javascripts.
     *
     * @return string
     */
    protected function renderEmbedJavascripts()
    {
        $output = '';

        foreach ($this->javascripts as $js) {
            $output .= $this->renderEmbedAsset($js['callable'], $js['context'], $js['blocks']);
        }

        $this->javascripts = array();

        return $output;
    }

    /**
     * Render all global embed stylesheets.
     *
     * @return string
     */
    protected function renderEmbedStylesheets()
    {
        $output = '';

        foreach ($this->stylesheets as $css) {
            $output .= $this->renderEmbedAsset($css['callable'], $css['context'], $css['blocks']);
        }

        $this->stylesheets = array();

        return $output;
    }

    /**
     * Render embed asset.
     *
     * @param array $callable
     * @param array $context
     * @param array $blocks
     *
     * @return string
     *
     * @throws \Twig_Error_Runtime When the callable argument is not an array with Twig_Tempate instance of the block
     */
    protected function renderEmbedAsset(array $callable, array $context, array $blocks)
    {
        if (2 !== count($callable) || !$callable[0] instanceof \Twig_Template || !is_string($callable[1])) {
            throw new \Twig_Error_Runtime('The callable argument must be an array with Twig_Template instance and name function of the block to rendering');
        }

        return $callable[0]->renderBlock($callable[1], $context, $blocks);
    }

    /**
     * Gets the tag position of embed asset.
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
