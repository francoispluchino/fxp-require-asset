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

/**
 * Util for require tag renderer.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class RequireUtil
{
    /**
     * Render the HTML tag.
     *
     * @param array  $attributes  The HTML attributes
     * @param string $htmlTag     The HTML tag name
     * @param bool   $shortEndTag Check if the end HTML tag must be in a short or long format
     *
     * @return string The output render
     */
    public static function renderHtmlTag(array $attributes, $htmlTag, $shortEndTag)
    {
        $output = '<'.$htmlTag;

        foreach ($attributes as $attr => $value) {
            if (static::isValidValue($value)) {
                $output .= ' '.$attr.'="'.$value.'"';
            }
        }

        $output .= $shortEndTag ? ' />' : '></'.$htmlTag.'>';

        return $output;
    }

    /**
     * Check if the value of attribute can be added in the render.
     *
     * @param mixed $value The attribute value
     *
     * @return bool
     */
    public static function isValidValue($value)
    {
        return !empty($value) && is_scalar($value) && !\is_bool($value);
    }
}
