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

use Fxp\Component\RequireAsset\Twig\Tag\Renderer\InlineTagRendererUtils;
use PHPUnit\Framework\TestCase;

/**
 * Inline Tag Renderer Utils Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 *
 * @internal
 */
final class InlineTagRendererUtilsTest extends TestCase
{
    public function testWrongCallable(): void
    {
        $this->expectException(\Fxp\Component\RequireAsset\Exception\Twig\BodyTagRendererException::class);
        $this->expectExceptionMessage('The callable argument must be an array with Twig_Template instance and name function of the block to rendering');

        InlineTagRendererUtils::renderBody([], [], []);
    }
}
