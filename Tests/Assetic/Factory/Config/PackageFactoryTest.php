<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Assetic\Factory\Config;

use Fxp\Component\RequireAsset\Assetic\Config\ConfigPackage;
use Fxp\Component\RequireAsset\Assetic\Config\ConfigPackageInterface;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtension;
use Fxp\Component\RequireAsset\Assetic\Factory\Config\PackageFactory;

/**
 * Package Factory Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class PackageFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function getCreateMethod()
    {
        return array(
            array('createConfig'),
            array('create'),
        );
    }

    /**
     * @dataProvider getCreateMethod
     *
     * @expectedException \Fxp\Component\RequireAsset\Exception\InvalidArgumentException
     */
    public function testCreateWhithoutName($method)
    {
        PackageFactory::$method(array());
    }

    /**
     * @dataProvider getCreateMethod
     */
    public function testCreate($method)
    {
        $config = array(
            'name' => 'package1',
            'source_path' => 'SOURCE_PATH',
            'source_base' => 'SOURCE_BASE',
            'extensions' => array(
                'js' => array(),
            ),
            'replace_default_extensions' => false,
            'patterns' => array(
                '*/pattern/*',
            ),
            'replace_default_patterns' => false,
        );
        $defaultExts = array(
            'css' => new FileExtension('css'),
        );
        $defaultPatterns = array(
            '*/default/*',
        );

        $pkg = PackageFactory::$method($config, $defaultExts, $defaultPatterns);

        if ('createConfig' === $method) {
            /* @var ConfigPackageInterface $pkg */
            $this->assertSame($config['replace_default_extensions'], $pkg->replaceDefaultExtensions());
            $this->assertSame($config['replace_default_patterns'], $pkg->replaceDefaultPatterns());
            $pkg->getPackage();
        }

        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PackageInterface', $pkg);
        $this->assertSame($config['name'], $pkg->getName());
        $this->assertSame($config['source_path'], $pkg->getSourcePath());
        $this->assertSame($config['source_base'], $pkg->getSourceBase());
        $this->assertSame(array_merge($defaultPatterns, $config['patterns']), $pkg->getPatterns());
        $this->assertEquals(array(
            'css' => new FileExtension('css'),
            'js' => new FileExtension('js'),
        ), $pkg->getExtensions());
    }

    /**
     * @dataProvider getCreateMethod
     */
    public function testCreateWithExtensionList($method)
    {
        $config = array(
            'name' => 'package1',
            'source_path' => 'SOURCE_PATH',
            'source_base' => 'SOURCE_BASE',
            'extensions' => array(
                'js',
            ),
        );
        $pkg = PackageFactory::$method($config);

        if ('createConfig' === $method) {
            /* @var ConfigPackageInterface $pkg */
            $pkg->getPackage();
        }

        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PackageInterface', $pkg);
        $this->assertSame($config['name'], $pkg->getName());
        $this->assertSame($config['source_path'], $pkg->getSourcePath());
        $this->assertSame($config['source_base'], $pkg->getSourceBase());
        $this->assertEquals(array(
            'js' => new FileExtension('js'),
        ), $pkg->getExtensions());
    }

    /**
     * @dataProvider getCreateMethod
     */
    public function testCreateWhithReplaceDefault($method)
    {
        $config = array(
            'name' => 'package1',
            'source_path' => 'SOURCE_PATH',
            'source_base' => 'SOURCE_BASE',
            'extensions' => array(
                'js' => array(),
            ),
            'replace_default_extensions' => true,
            'patterns' => array(
                '*/pattern/*',
            ),
            'replace_default_patterns' => true,
        );
        $defaultExts = array(
            'css' => new FileExtension('css'),
        );
        $defaultPatterns = array(
            '*/default/*',
        );

        $pkg = PackageFactory::$method($config, $defaultExts, $defaultPatterns);

        if ('createConfig' === $method) {
            /* @var ConfigPackageInterface $pkg */
            $this->assertSame($config['replace_default_extensions'], $pkg->replaceDefaultExtensions());
            $this->assertSame($config['replace_default_patterns'], $pkg->replaceDefaultPatterns());
            $pkg->getPackage();
        }

        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PackageInterface', $pkg);
        $this->assertSame($config['name'], $pkg->getName());
        $this->assertSame($config['source_path'], $pkg->getSourcePath());
        $this->assertSame($config['source_base'], $pkg->getSourceBase());
        $this->assertSame($config['patterns'], $pkg->getPatterns());
        $this->assertEquals(array(
            'js' => new FileExtension('js'),
        ), $pkg->getExtensions());
    }

    public function testConvertToArraySimple()
    {
        $pkg = new ConfigPackage('NAME');
        $config = $ext = PackageFactory::convertToArray($pkg);
        $valid = array(
            'name' => 'NAME',
        );

        $this->assertInternalType('array', $config);
        $this->assertEquals($valid, $config);
    }

    public function testConvertToArraySimpleWithAllFields()
    {
        $pkg = new ConfigPackage('NAME', 'SOURCE_PATH', 'SOURCE_BASE');
        $config = $ext = PackageFactory::convertToArray($pkg, true);
        $valid = array(
            'name' => 'NAME',
            'source_path' => 'SOURCE_PATH',
            'source_base' => 'SOURCE_BASE',
            'replace_default_extensions' => false,
            'replace_default_patterns' => false,
            'extensions' => array(),
            'patterns' => array(),
        );

        $this->assertInternalType('array', $config);
        $this->assertEquals($valid, $config);
    }

    public function testConvertToArrayComplexe()
    {
        $pkg = new ConfigPackage('NAME', 'SOURCE_PATH', 'SOURCE_BASE');
        $pkg->addExtension('js');
        $pkg->setReplaceDefaultExtensions(true);
        $pkg->setReplaceDefaultPatterns(true);

        $config = $ext = PackageFactory::convertToArray($pkg);
        $valid = array(
            'name' => 'NAME',
            'source_path' => 'SOURCE_PATH',
            'source_base' => 'SOURCE_BASE',
            'replace_default_extensions' => true,
            'replace_default_patterns' => true,
            'extensions' => array(
                'js' => array(
                    'name' => 'js',
                ),
            ),
        );

        $this->assertInternalType('array', $config);
        $this->assertEquals($valid, $config);
    }

    public function testConvertToArrayComplexeWithAllFields()
    {
        $pkg = new ConfigPackage('NAME', 'SOURCE_PATH', 'SOURCE_BASE');
        $pkg->addExtension('js');
        $pkg->setReplaceDefaultExtensions(true);
        $pkg->setReplaceDefaultPatterns(true);

        $config = $ext = PackageFactory::convertToArray($pkg, true);
        $valid = array(
            'name' => 'NAME',
            'source_path' => 'SOURCE_PATH',
            'source_base' => 'SOURCE_BASE',
            'replace_default_extensions' => true,
            'replace_default_patterns' => true,
            'extensions' => array(
                'js' => array(
                    'name' => 'js',
                ),
            ),
            'patterns' => array(),
        );

        $this->assertInternalType('array', $config);
        $this->assertEquals($valid, $config);
    }

    public function testMerge()
    {
        $pkg1 = new ConfigPackage('NAME');
        $pkg2 = new ConfigPackage('NAME', 'SOURCE_PATH', 'SOURCE_BASE');

        $pkg1->setReplaceDefaultExtensions(true);
        $pkg2->setReplaceDefaultPatterns(true);

        $pkg1->addExtension('js');
        $pkg2->addExtension('css');

        $pkg = PackageFactory::merge(array($pkg1, $pkg2));

        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\ConfigPackageInterface', $pkg);
        $this->assertSame('NAME', $pkg->getName());
        $this->assertSame('SOURCE_PATH', $pkg->getSourcePath());
        $this->assertSame('SOURCE_BASE', $pkg->getSourceBase());
        $this->assertTrue($pkg->replaceDefaultExtensions());
        $this->assertTrue($pkg->replaceDefaultPatterns());
        $this->assertEquals(array(
            'js' => new FileExtension('js'),
            'css' => new FileExtension('css'),
        ), $pkg->getExtensions());
        $this->assertEquals(array(), $pkg->getPatterns());
    }
}
