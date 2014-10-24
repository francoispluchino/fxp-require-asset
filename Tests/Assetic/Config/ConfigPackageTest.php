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
use Fxp\Component\RequireAsset\Assetic\Config\ConfigPackageInterface;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtension;

/**
 * Config Package Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class ConfigPackageTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $cpkg = new ConfigPackage('NAME', 'SOURCE_PATH', 'SOURCE_BASE');

        $cpkg->addExtension('ext1');
        $cpkg->addExtension('ext2');
        $cpkg->addExtension('ext3');

        $this->assertTrue($cpkg->hasExtension('ext1'));
        $this->assertTrue($cpkg->hasExtension('ext2'));
        $this->assertTrue($cpkg->hasExtension('ext3'));
        $this->assertFalse($cpkg->hasExtension('nonexistingextension'));

        $cpkg->addPattern('pattern1/*');
        $cpkg->addPattern('pattern2/*');
        $cpkg->addPattern('pattern3/*');

        $this->assertTrue($cpkg->hasPattern('pattern1/*'));
        $this->assertTrue($cpkg->hasPattern('pattern2/*'));
        $this->assertTrue($cpkg->hasPattern('pattern3/*'));
        $this->assertFalse($cpkg->hasPattern('nonexistingpattern'));

        $cpkg->removeExtension('ext2');

        $this->assertTrue($cpkg->hasExtension('ext1'));
        $this->assertFalse($cpkg->hasExtension('ext2'));
        $this->assertTrue($cpkg->hasExtension('ext3'));

        $cpkg->removePattern('pattern2/*');

        $this->assertTrue($cpkg->hasPattern('pattern1/*'));
        $this->assertFalse($cpkg->hasPattern('pattern2/*'));
        $this->assertTrue($cpkg->hasPattern('pattern3/*'));

        $this->assertSame('NAME', $cpkg->getName());
        $this->assertSame('SOURCE_PATH', $cpkg->getSourcePath());
        $this->assertSame('SOURCE_BASE', $cpkg->getSourceBase());

        $validExts = array(
            'ext1' => new FileExtension('ext1'),
            'ext3' => new FileExtension('ext3'),
        );
        $this->assertEquals($validExts['ext1'], $cpkg->getExtension('ext1'));
        $this->assertEquals($validExts['ext3'], $cpkg->getExtension('ext3'));
        $this->assertEquals($validExts, $cpkg->getExtensions());

        $validPatterns = array(
            'pattern1/*',
            'pattern3/*',
        );
        $this->assertEquals($validPatterns, $cpkg->getPatterns());

        $this->assertFalse($cpkg->replaceDefaultExtensions());
        $this->assertFalse($cpkg->replaceDefaultPatterns());

        $cpkg->setReplaceDefaultExtensions(true);
        $cpkg->setReplaceDefaultPatterns(true);

        $this->assertTrue($cpkg->replaceDefaultExtensions());
        $this->assertTrue($cpkg->replaceDefaultPatterns());
    }

    public function testAddExtensionWithLockedManager()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\BadMethodCallException');

        $cpkg = new ConfigPackage('NAME', 'SOURCE_PATH');
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PackageInterface', $cpkg->getPackage());

        $cpkg->addExtension('ext');
    }

    public function testRemoveExtensionWithLockedManager()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\BadMethodCallException');

        $cpkg = new ConfigPackage('NAME', 'SOURCE_PATH');
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PackageInterface', $cpkg->getPackage());

        $cpkg->removeExtension('ext');
    }

    public function testAddPatternWithLockedManager()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\BadMethodCallException');

        $cpkg = new ConfigPackage('NAME', 'SOURCE_PATH');
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PackageInterface', $cpkg->getPackage());

        $cpkg->addPattern('pattern');
    }

    public function testRemovePatternWithLockedManager()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\BadMethodCallException');

        $cpkg = new ConfigPackage('NAME', 'SOURCE_PATH');
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PackageInterface', $cpkg->getPackage());

        $cpkg->removePattern('pattern');
    }

    public function testReplaceDefaultExtensionsWithLockedManager()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\BadMethodCallException');

        $cpkg = new ConfigPackage('NAME', 'SOURCE_PATH');
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PackageInterface', $cpkg->getPackage());

        $cpkg->setReplaceDefaultExtensions(true);
    }

    public function testReplaceDefaultPatternsWithLockedManager()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\BadMethodCallException');

        $cpkg = new ConfigPackage('NAME', 'SOURCE_PATH');
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PackageInterface', $cpkg->getPackage());

        $cpkg->setReplaceDefaultPatterns(true);
    }

    public function testAddExtensionWithArguments()
    {
        $cpkg = new ConfigPackage('NAME');

        $c1 = $this->getFileExtensionConfig1();
        $cpkg->addExtension($c1['name'], $c1['options'], $c1['filters'], $c1['extension'], $c1['debug'], $c1['exclude']);
        $this->validateAddExtensionStep1($cpkg, $c1);

        // merge config
        $c2 = $this->getFileExtensionConfig2();
        $cpkg->addExtension($c2['name'], $c2['options'], $c2['filters'], $c2['extension'], $c2['debug'], $c2['exclude']);
        $this->validateAddExtensionStep2($cpkg, $c1, $c2);
    }

    public function testAddExtensionWithArrayConfig()
    {
        $cpkg = new ConfigPackage('NAME');

        $c1 = $this->getFileExtensionConfig1();
        $cpkg->addExtension($c1);
        $this->validateAddExtensionStep1($cpkg, $c1);

        // merge config
        $c2 = $this->getFileExtensionConfig2();
        $cpkg->addExtension($c2);
        $this->validateAddExtensionStep2($cpkg, $c1, $c2);
    }

    public function testAddExtensionWithInstance()
    {
        $cpkg = new ConfigPackage('NAME');

        $c1 = $this->getFileExtensionConfig1();
        $c1i = new FileExtension($c1['name'], $c1['options'], $c1['filters'], $c1['extension'], $c1['debug'], $c1['exclude']);
        $cpkg->addExtension($c1i);
        $this->validateAddExtensionStep1($cpkg, $c1);

        // merge config
        $c2 = $this->getFileExtensionConfig2();
        $c2i = new FileExtension($c2['name'], $c2['options'], $c2['filters'], $c2['extension'], $c2['debug'], $c2['exclude']);
        $cpkg->addExtension($c2i);
        $this->validateAddExtensionStep2($cpkg, $c1, $c2);
    }

    /**
     * @param ConfigPackageInterface $cpkg
     * @param array                  $c1
     */
    protected function validateAddExtensionStep1(ConfigPackageInterface $cpkg, array $c1)
    {
        $ext = $cpkg->getExtension($c1['name']);
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\FileExtensionInterface', $ext);
        $this->assertSame($c1['name'], $ext->getName());
        $this->assertSame($c1['options'], $ext->getOptions());
        $this->assertSame($c1['filters'], $ext->getFilters());
        $this->assertSame($c1['debug'], $ext->isDebug());
        $this->assertSame($c1['exclude'], $ext->isExclude());
    }

    /**
     * @param ConfigPackageInterface $cpkg
     * @param array                  $c1
     * @param array                  $c2
     */
    protected function validateAddExtensionStep2(ConfigPackageInterface $cpkg, array $c1, array $c2)
    {
        $ext = $cpkg->getExtension($c2['name']);
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\FileExtensionInterface', $ext);
        $this->assertSame($c1['name'], $ext->getName());
        $this->assertSame(array_merge($c1['options'], $c2['options']), $ext->getOptions());
        $this->assertSame(array_merge($c1['filters'], $c2['filters']), $ext->getFilters());
        $this->assertSame($c2['debug'], $ext->isDebug());
        $this->assertSame($c1['exclude'], $ext->isExclude());
    }

    /**
     * @return array
     */
    protected function getFileExtensionConfig1()
    {
        return array(
            'name'      => 'ext',
            'options'   => array('option1' => 'value1'),
            'filters'   => array('filter1'),
            'extension' => null,
            'debug'     => false,
            'exclude'   => true,
        );
    }

    /**
     * @return array
     */
    protected function getFileExtensionConfig2()
    {
        return array(
            'name'      => 'ext',
            'options'   => array('option2' => 'value2'),
            'filters'   => array('filter2'),
            'extension' => 'otherext',
            'debug'     => true,
            'exclude'   => false,
        );
    }
}
