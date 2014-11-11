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
 * Inline script tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InlineScriptTag extends AbstractInlineTag
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'script';
    }
}
