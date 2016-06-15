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

/**
 * Inline Tag Renderer Utils Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InlineTagRendererUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\Twig\BodyTagRendererException
     * @expectedExceptionMessage The callable argument must be an array with Twig_Template instance and name function of the block to rendering
     */
    public function testWrongCallable()
    {
        InlineTagRendererUtils::renderBody(array(), array(), array());
    }
}
