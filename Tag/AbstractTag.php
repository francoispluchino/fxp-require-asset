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
 * Abstract template tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractTag implements TagInterface
{
    /**
     * @var string|null
     */
    protected $position;

    /**
     * @var int
     */
    protected $lineno;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * Constructor.
     *
     * @param string|null $position The positon in template
     * @param int         $lineno   The template line
     * @param string|null $name     The template logical name
     */
    public function __construct($position = null, $lineno = -1, $name = null)
    {
        $this->position = $position;
        $this->lineno = $lineno;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function getTagPositionName()
    {
        return strtolower($this->getCategory().':'.$this->getType().':'.$this->getPosition());
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateLine()
    {
        return $this->lineno;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateName()
    {
        return $this->name;
    }
}
