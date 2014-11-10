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

use Fxp\Component\RequireAsset\Exception\Twig\AssetRenderException;
use Fxp\Component\RequireAsset\Twig\Asset\TwigAssetInterface;
use Fxp\Component\RequireAsset\Twig\Asset\TwigInlineAssetInterface;

/**
 * Asset inline renderer.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetInlineRenderer implements AssetRendererInterface
{
    /**
     * {@inheritdoc}
     */
    public function render(TwigAssetInterface $asset)
    {
        /* @var TwigInlineAssetInterface $asset */
        $callable = $asset->getCallable();

        if (2 !== count($callable) || !$callable[0] instanceof \Twig_Template || !is_string($callable[1])) {
            throw new AssetRenderException('The callable argument must be an array with Twig_Template instance and name function of the block to rendering', $asset->getLineno(), $asset->getFilename());
        }

        /* @var \Twig_Template $template */
        $template = $callable[0];

        return $template->renderBlock($callable[1], $asset->getContext(), $asset->getBlocks());
    }

    /**
     * {@inheritdoc}
     */
    public function validate(TwigAssetInterface $asset)
    {
        return $asset instanceof TwigInlineAssetInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        // nothing
    }
}
