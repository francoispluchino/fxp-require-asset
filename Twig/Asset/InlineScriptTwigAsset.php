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
 * Config of twig inline script asset.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InlineScriptTwigAsset extends AbstractInlineTwigAsset
{
    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return 'script';
    }
}
