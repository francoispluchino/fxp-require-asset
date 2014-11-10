<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\Extension;

use Assetic\AssetManager;
use Fxp\Component\RequireAsset\Twig\Renderer\AssetInlineRenderer;
use Fxp\Component\RequireAsset\Twig\Renderer\AssetRequireRenderer;

/**
 * Core asset extension.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class CoreAssetExtension extends AssetExtension
{
    /**
     * Constructor.
     *
     * @param AssetManager $manager The assetic manager
     */
    public function __construct(AssetManager $manager)
    {
        parent::__construct();

        $this->setRenderers(array(
            new AssetInlineRenderer(),
            new AssetRequireRenderer($manager),
        ));
    }
}
