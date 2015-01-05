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
 * Interface of require template tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface RequireTagInterface extends TagInterface
{
    /**
     * Get the assetic name of this template tag.
     *
     * @return string
     */
    public function getAsseticName();

    /**
     * Get the HTML attributes.
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Get the asset path.
     *
     * @return string
     */
    public function getPath();

    /**
     * Set the inputs.
     * Use only by the order of the renderer.
     *
     * @param array $inputs The inputs
     *
     * @return self
     */
    public function setInputs(array $inputs);

    /**
     * Get the inputs.
     * Use only by the order of the renderer.
     *
     * @return array
     */
    public function getInputs();

    /**
     * Check if the end HTML tag must be in a short or long format.
     *
     * @return bool
     */
    public function shortEndTag();

    /**
     * Get the HTML tag.
     *
     * @return string
     */
    public function getHtmlTag();

    /**
     * Get the HTML attribute name for the external link.
     *
     * @return string
     */
    public function getLinkAttribute();

    /**
     * Set if the tag is optional.
     *
     * @param bool $optional The optional require tag
     *
     * @return self
     */
    public function setOptional($optional);

    /**
     * Check if the tag is optional.
     *
     * @return bool
     */
    public function isOptional();
}
