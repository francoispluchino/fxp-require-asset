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

/**
 * Abstract config of twig require asset.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractTwigAsset implements TwigAssetInterface
{
    /**
     * @var string|null
     */
    protected $position;

    /**
     * Constructor.
     *
     * @param string|null $position The positon in template
     */
    public function __construct($position = null)
    {
        $this->position = $position;
    }

    /**
     * {@inheritDoc}
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Get the tag position name of asset.
     *
     * @return string The formatted tag position
     */
    public function getTagPositionName()
    {
        return strtolower($this->getCategory() . ':' . $this->getType() . ':' . $this->getPosition());
    }
}
