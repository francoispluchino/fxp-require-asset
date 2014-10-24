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

use Fxp\Component\RequireAsset\Assetic\Config\ConfigPackage;
use Fxp\Component\RequireAsset\Assetic\Config\Package;

/**
 * Package Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class PackageTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidSourcePath()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\InvalidArgumentException');

        new Package(new ConfigPackage('NAME'));
    }

    public function testBasic()
    {
        $cpkg = new ConfigPackage('NAME', 'SOURCE_PATH', 'SOURCE_BASE');
        $cpkg->addExtension('ext1');
        $cpkg->addPattern('pattern1');

        $pkg = new Package($cpkg);

        $this->assertSame($cpkg->getName(), $pkg->getName());
        $this->assertSame($cpkg->getSourcePath(), $pkg->getSourcePath());
        $this->assertSame($cpkg->getSourceBase(), $pkg->getSourceBase());
        $this->assertTrue($pkg->hasExtension('ext1'));
        $this->assertSame($cpkg->getExtension('ext1'), $pkg->getExtension('ext1'));
        $this->assertSame($cpkg->getExtensions(), $pkg->getExtensions());
        $this->assertTrue($pkg->hasPattern('pattern1'));
        $this->assertSame($cpkg->getPatterns(), $pkg->getPatterns());
    }

    public function testWithoutSourceBase()
    {
        $cpkg = new ConfigPackage('NAME', 'path/to/source');
        $pkg = new Package($cpkg);

        $this->assertSame('source', $pkg->getSourceBase());
    }

    public function testNonExistentFileExtension()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\InvalidConfigurationException');

        $cpkg = new ConfigPackage('NAME', 'SOURCE_PATH');
        $pkg = new Package($cpkg);
        $pkg->getExtension('ext1');
    }

    public function testClone()
    {
        $cpkg = new ConfigPackage('NAME', 'path/to/source');
        $cpkg->addExtension('ext');
        $pkg = new Package($cpkg);

        $pkg2 = clone $pkg;

        $this->assertEquals($pkg, $pkg2);
        $this->assertNotSame($pkg, $pkg2);
        $this->assertEquals($pkg->getExtensions(), $pkg2->getExtensions());
        $this->assertNotSame($pkg->getExtensions(), $pkg2->getExtensions());
    }
}
