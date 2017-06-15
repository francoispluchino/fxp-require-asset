<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Assetic\Config;

use Fxp\Component\RequireAsset\Assetic\Config\FileExtension;
use PHPUnit\Framework\TestCase;

/**
 * File Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FileExtensionTest extends TestCase
{
    public function testBasic()
    {
        $ext = new FileExtension('ext');

        $this->assertSame('ext', $ext->getName());
        $this->assertSame(array(), $ext->getOptions());
        $this->assertSame(array(), $ext->getFilters());
        $this->assertSame('ext', $ext->getOutputExtension());
        $this->assertFalse($ext->isDebug());
        $this->assertFalse($ext->isExclude());
    }

    public function testSameOutputExtensionName()
    {
        $ext = new FileExtension('ext', array(), array(), 'ext');

        $this->assertSame('ext', $ext->getOutputExtension());
    }

    public function testDifferentOutputExtensionName()
    {
        $ext = new FileExtension('ext', array(), array(), 'other');

        $this->assertSame('other', $ext->getOutputExtension());
    }
}
