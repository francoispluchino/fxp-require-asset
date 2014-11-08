<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\Asset;

use Assetic\Factory\LazyAssetManager;
use Assetic\Util\VarUtils;
use Fxp\Component\RequireAsset\Assetic\Util\Utils;
use Fxp\Component\RequireAsset\Exception\InvalidArgumentException;
use Fxp\Component\RequireAsset\Twig\Asset\Conditional\ConditionalRenderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\Helper\CoreAssetsHelper;

/**
 * Abstract config of twig require asset.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractRequireTwigAsset extends AbstractTwigAsset implements TwigRequireAssetInterface
{
    /**
     * @var string
     */
    protected $asset;

    /**
     * @var string
     */
    protected $asseticName;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var LazyAssetManager
     */
    protected $manager;

    /**
     * @var CoreAssetsHelper
     */
    protected $helper;

    /**
     *
     * @param string      $asset      The asset source path
     * @param array       $attributes The HTML attributes
     * @param string|null $position   The position in the template
     */
    public function __construct($asset, array $attributes = array(), $position = null)
    {
        parent::__construct($position);

        $this->asset = $asset;
        $this->asseticName = Utils::formatName($asset);
        $this->attributes = $attributes;
    }

    /**
     * {@inheritDoc}
     */
    public function getCategory()
    {
        return 'require';
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $managerId = 'assetic.asset_manager';
        $helperId = 'templating.helper.assets';
        $this->manager = $container->get($managerId);
        $this->helper = $container->get($helperId);
    }

    /**
     * {@inheritDoc}
     */
    public function getAsseticName()
    {
        return $this->asseticName;
    }

    /**
     * {@inheritDoc}
     */
    public function render(ConditionalRenderInterface $conditional = null)
    {
        if (null === $conditional) {
            throw new InvalidArgumentException(sprintf('The conditional render is required for the %s asset "%s"', $this->getCategory(), $this->getAsset()));
        }

        return $conditional->isValid($this)
            ? $this->preRender()
            : '';
    }

    /**
     * Prepare the render and do the render.
     *
     * @return string The output render
     */
    protected function preRender()
    {
        if (!$this->manager->has($this->getAsseticName())) {
            return '';
        }

        $assetFile = $this->manager->get($this->getAsseticName());
        $target = str_replace('_controller/', '', $assetFile->getTargetPath());
        $target = VarUtils::resolve($target, $assetFile->getVars(), $assetFile->getValues());
        $target = $this->helper->getUrl($target);
        $attributes = $this->getAttributes();
        $attributes[$this->getLinkAttribute()] = $target;

        return $this->doRender($attributes);
    }

    /**
     * Do render.
     *
     * @param array $attributes The HTML attributes
     *
     * @return string The output render
     */
    protected function doRender(array $attributes)
    {
        $output = '<' . $this->getHtmlTag();

        foreach ($attributes as $attr => $value) {
            if (null !== $value) {
                $output .= ' ' . $attr . '="' . $value . '"';
            }
        }

        if ($this->shortEndTag()) {
            $output .= ' />';
        } else {
            $output .= '></' . $this->getHtmlTag() . '>';
        }

        return $output;
    }

    /**
     * Get the HTML attributes.
     *
     * @return array
     */
    protected function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get the asset path.
     *
     * @return string
     */
    protected function getAsset()
    {
        return $this->asset;
    }

    /**
     * Check if the end tag must be in a short or long format.
     *
     * @return bool
     */
    abstract protected function shortEndTag();

    /**
     * Get the HTML tag.
     *
     * @return string
     */
    abstract protected function getHtmlTag();

    /**
     * Get the HTML attribute name for the external link.
     *
     * @return string
     */
    abstract protected function getLinkAttribute();
}
