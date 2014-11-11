<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\Tag\Renderer;

use Fxp\Component\RequireAsset\Exception\Twig\BodyTagRendererException;

/**
 * Template inline tag renderer.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class InlineTagRendererUtils
{
    /**
     * Render the body of template tag.
     *
     * @param array       $callable The callable
     * @param array       $context  The twig context
     * @param array       $blocks   The twig blocks
     * @param int         $lineno   The twig lineno
     * @param string|null $filename The twig filename
     *
     * @return string
     *
     * @throws BodyTagRendererException
     */
    public static function renderBody(array $callable, array $context, array $blocks, $lineno = -1, $filename = null)
    {
        if (2 !== count($callable) || !$callable[0] instanceof \Twig_Template || !is_string($callable[1])) {
            throw new BodyTagRendererException('The callable argument must be an array with Twig_Template instance and name function of the block to rendering', $lineno, $filename);
        }

        /* @var \Twig_Template $template */
        $template = $callable[0];

        return $template->renderBlock($callable[1], $context, $blocks);
    }
}
