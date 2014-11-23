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
    protected $filename;

    /**
     * Constructor.
     *
     * @param string|null $position The positon in template
     * @param int         $lineno   The template lineno
     * @param string|null $filename The template filename
     */
    public function __construct($position = null, $lineno = -1, $filename = null)
    {
        $this->position = $position;
        $this->lineno = $lineno;
        $this->filename = $filename;
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
    public function getLineno()
    {
        return $this->lineno;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename()
    {
        return $this->filename;
    }
}
