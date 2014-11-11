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

use Fxp\Component\RequireAsset\Assetic\Util\Utils;

/**
 * Abstract require template tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractRequireTag extends AbstractTag implements RequireTagInterface
{
    /**
     * @var string
     */
    protected $assetPath;

    /**
     * @var string
     */
    protected $asseticName;

    /**
     * @var array
     */
    protected $attributes;

    /**
     *
     * @param string      $assetPath  The asset source path
     * @param array       $attributes The HTML attributes
     * @param string|null $position   The position in the template
     * @param int         $lineno     The template lineno
     * @param string|null $filename   The template filename
     */
    public function __construct($assetPath, array $attributes = array(), $position = null, $lineno = -1, $filename = null)
    {
        parent::__construct($position, $lineno, $filename);

        $this->assetPath = $assetPath;
        $this->asseticName = Utils::formatName($assetPath);
        $this->attributes = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory()
    {
        return 'require';
    }

    /**
     * {@inheritdoc}
     */
    public function getAsseticName()
    {
        return $this->asseticName;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->assetPath;
    }
}
