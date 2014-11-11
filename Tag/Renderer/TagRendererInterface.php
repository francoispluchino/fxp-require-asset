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

/**
 * Interface of template tag renderer.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface TagRendererInterface
{
    /**
     * Render the template tag.
     *
     * @param TagInterface $tag The template tag
     *
     * @return string
     */
    public function render(TagInterface $tag);

    /**
     * Check if the renderer is valid for the template tag.
     *
     * @param TagInterface $tag The template tag
     *
     * @return bool
     */
    public function validate(TagInterface $tag);

    /**
     * Reset the renderer.
     *
     * @return void
     */
    public function reset();
}
