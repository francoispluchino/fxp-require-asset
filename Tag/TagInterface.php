<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tag;

/**
 * Interface of template tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface TagInterface
{
    /**
     * Get the category of template tag.
     *
     * @return string
     */
    public function getCategory();

    /**
     * Get the type of template tag.
     *
     * @return string
     */
    public function getType();

    /**
     * Get the position of template tag.
     *
     * @return null|string
     */
    public function getPosition();

    /**
     * Get the name of tag position.
     *
     * @return string The formatted tag position
     */
    public function getTagPositionName();

    /**
     * Get the template line.
     *
     * @return int
     */
    public function getTemplateLine();

    /**
     * Get the template logical name.
     *
     * @return null|string
     */
    public function getTemplateName();
}
