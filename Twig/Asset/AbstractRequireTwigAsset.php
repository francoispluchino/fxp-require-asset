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

use Fxp\Component\RequireAsset\Assetic\Util\Utils;

/**
 * Abstract config of twig require asset.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractRequireTwigAsset extends AbstractTwigAsset implements TwigRequireAssetInterface
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
     * @param int         $lineno     The twig lineno
     * @param string|null $filename   The twig filename
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
