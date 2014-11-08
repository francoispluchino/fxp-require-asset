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
use Fxp\Component\RequireAsset\Twig\Asset\Conditional\ConditionalRenderInterface;
use Fxp\Component\RequireAsset\Twig\Asset\Conditional\UniqueRequireAsset;
use Fxp\Component\RequireAsset\Twig\Asset\TwigAssetInterface;
use Fxp\Component\RequireAsset\Twig\Asset\TwigContainerAwareInterface;
use Fxp\Component\RequireAsset\Twig\TokenParser\InlineScriptTokenParser;
use Fxp\Component\RequireAsset\Twig\TokenParser\InlineStyleTokenParser;
use Fxp\Component\RequireAsset\Twig\TokenParser\RequireScriptTokenParser;
use Fxp\Component\RequireAsset\Twig\TokenParser\RequireStyleTokenParser;
use Fxp\Component\RequireAsset\Twig\TwigFunction\TwigAssetFunction;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * AssetExtension extends Twig with global assets rendering capabilities.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    public $container;

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
            new TwigAssetFunction('inlineScriptsPosition',  array($this, 'createAssetPosition'), array('category' => 'inline',  'type' => 'script')),
            new TwigAssetFunction('inlineStylesPosition',   array($this, 'createAssetPosition'), array('category' => 'inline',  'type' => 'style')),
            new TwigAssetFunction('requireScriptsPosition', array($this, 'createAssetPosition'), array('category' => 'require', 'type' => 'script')),
            new TwigAssetFunction('requireStylesPosition',  array($this, 'createAssetPosition'), array('category' => 'require', 'type' => 'style')),
            new \Twig_SimpleFunction('renderAssets',        array($this, 'renderAssets'),        array('is_safe' => array('html'))),
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
     * Add twig asset.
     *
     * @param TwigAssetInterface $asset
     *
     * @return self
     */
    public function addAsset(TwigAssetInterface $asset)
    {
        $this->contents[$asset->getTagPositionName()][] = $asset;

        return $this;
    }

    /**
     * Create the asset position tag to included in the twig template.
     *
     * @param string      $category The twig asset category
     * @param string      $type     The asset type
     * @param string|null $position The name of tag position in the twig template
     *
     * @return string
     *
     * @throws InvalidTwigArgumentException When tag position is already defined in template
     */
    public function createAssetPosition($category, $type, $position = null)
    {
        $pos = trim($position, '_');
        $pos = strlen($pos) > 0 ? '_' . $pos : '';
        $tag = strtoupper($category . '_' . $type . $pos);

        if (isset($this->tagPositions[$tag])) {
            throw new InvalidTwigArgumentException($category, $type, $position);
        }

        $this->tagPositions[$tag] = $this->formatTagPosition($category, $type, $position);

        return $this->getTagPosition($tag);
    }

    /**
     * Render all assets.
     *
     * Replaces the current buffer with the new edited buffer content.
     */
    public function renderAssets()
    {
        $output = ob_get_contents();
        $conditional = new UniqueRequireAsset();

        foreach ($this->tagPositions as $name => $contentType) {
            $output = $this->doRenderAssets($name, $contentType, $output, $conditional);
        }

        ob_clean();
        echo $output;

        $this->validateRenderAssets();
    }

    /**
     * Do render the assets by type.
     *
     * @param string                     $name
     * @param string                     $contentType
     * @param string                     $output
     * @param ConditionalRenderInterface $conditional The conditional render instance
     *
     * @return string The output with replaced asset tag position
     */
    protected function doRenderAssets($name, $contentType, $output, ConditionalRenderInterface $conditional)
    {
        $content = '';

        if (isset($this->contents[$contentType])) {
            /* @var TwigAssetInterface|TwigContainerAwareInterface $asset */
            foreach ($this->contents[$contentType] as $asset) {
                if ($asset instanceof TwigContainerAwareInterface) {
                    $asset->setContainer($this->container);
                }

                $content .= $asset->render($conditional);
            }
            unset($this->contents[$contentType]);
        }

        return str_replace($this->getTagPosition($name), $content, $output);
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
