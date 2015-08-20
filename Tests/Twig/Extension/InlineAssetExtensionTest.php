<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Twig\Extension;

/**
 * Inline Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InlineAssetExtensionTest extends AbstractAssetExtensionTest
{
    /**
     * @dataProvider getInlineTwigTags
     *
     * @param string $tag
     */
    public function testEmptyBody($tag)
    {
        $this->getTemplate($tag, 'empty_body.html.twig');
    }

    /**
     * @dataProvider getInlineTwigTags
     *
     * @param string $tag
     */
    public function testTwigTags($tag)
    {
        $this->doValidTagTest($tag);
    }
}
