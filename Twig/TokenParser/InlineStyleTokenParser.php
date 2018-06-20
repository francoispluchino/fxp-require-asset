<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\TokenParser;

use Fxp\Component\RequireAsset\Tag\InlineStyleTag;

/**
 * Token Parser for the 'inline_style' tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InlineStyleTokenParser extends AbstractInlineTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'inline_style';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTagClass()
    {
        return InlineStyleTag::class;
    }
}
