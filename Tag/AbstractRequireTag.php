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
     * @var array
     */
    protected $inputs;

    /**
     * @var bool
     */
    protected $optional;

    /**
     * @param string      $assetPath  The asset source path
     * @param array       $attributes The HTML attributes
     * @param string|null $position   The position in the template
     * @param int         $lineno     The template lineno
     * @param string|null $filename   The template filename
     */
    public function __construct($assetPath, array $attributes = array(), $position = null, $lineno = -1, $filename = null)
    {
        parent::__construct($position, $lineno, $filename);

        $this->optional = false;
        $assetPath = $this->checkOptionalPath($assetPath);
        $this->assetPath = $assetPath;
        $this->asseticName = Utils::formatName($assetPath);
        $this->attributes = $attributes;
        $this->inputs = array();
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

    /**
     * {@inheritdoc}
     */
    public function setInputs(array $inputs)
    {
        $this->inputs = $inputs;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInputs()
    {
        return $this->inputs;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptional($optional)
    {
        $this->optional = $optional;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return $this->optional;
    }

    /**
     * Check optional asset path.
     *
     * @param string $assetPath The asset path
     *
     * @return string The asset path without '?'
     */
    protected function checkOptionalPath($assetPath)
    {
        if (0 === strpos($assetPath, '?')) {
            $assetPath = ltrim($assetPath, '?');
            $this->setOptional(true);
        }

        return $assetPath;
    }
}
