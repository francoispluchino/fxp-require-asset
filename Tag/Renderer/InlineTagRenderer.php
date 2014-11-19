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

use Fxp\Component\RequireAsset\Tag\TagInterface;
use Fxp\Component\RequireAsset\Tag\InlineTagInterface;

/**
 * Template inline tag renderer.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InlineTagRenderer implements TagRendererInterface
{
    /**
     * {@inheritdoc}
     */
    public function order(array $tags)
    {
        return $tags;
    }

    /**
     * {@inheritdoc}
     */
    public function render(TagInterface $tag)
    {
        /* @var InlineTagInterface $tag */

        return $tag->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(TagInterface $tag)
    {
        return $tag instanceof InlineTagInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        // nothing
    }
}
