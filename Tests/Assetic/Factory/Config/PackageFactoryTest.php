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
use PHPUnit\Framework\TestCase;

/**
 * Package Factory Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class PackageFactoryTest extends TestCase
{
    public function getCreateMethod()
    {
        return [
            ['createConfig'],
            ['create'],
        ];
    }

    /**
     * @dataProvider getCreateMethod
     *
     * @expectedException \Fxp\Component\RequireAsset\Exception\InvalidArgumentException
     *
     * @param string $method
     */
    public function testCreateWhithoutName($method)
    {
        PackageFactory::$method([]);
    }

    /**
     * @dataProvider getCreateMethod
     *
     * @param string $method
     */
    public function testCreate($method)
    {
        $config = [
            'name' => 'package1',
            'source_path' => 'SOURCE_PATH',
            'source_base' => 'SOURCE_BASE',
            'extensions' => [
                'js' => [],
            ],
            'replace_default_extensions' => false,
            'patterns' => [
                '*/pattern/*',
            ],
            'replace_default_patterns' => false,
        ];
        $defaultExts = [
            'css' => new FileExtension('css'),
        ];
        $defaultPatterns = [
            '*/default/*',
        ];

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
        $this->assertEquals([
            'css' => new FileExtension('css'),
            'js' => new FileExtension('js'),
        ], $pkg->getExtensions());
    }

    /**
     * @dataProvider getCreateMethod
     *
     * @param string $method
     */
    public function testCreateWithExtensionList($method)
    {
        $config = [
            'name' => 'package1',
            'source_path' => 'SOURCE_PATH',
            'source_base' => 'SOURCE_BASE',
            'extensions' => [
                'js',
            ],
        ];
        $pkg = PackageFactory::$method($config);

        if ('createConfig' === $method) {
            /* @var ConfigPackageInterface $pkg */
            $pkg->getPackage();
        }

        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PackageInterface', $pkg);
        $this->assertSame($config['name'], $pkg->getName());
        $this->assertSame($config['source_path'], $pkg->getSourcePath());
        $this->assertSame($config['source_base'], $pkg->getSourceBase());
        $this->assertEquals([
            'js' => new FileExtension('js'),
        ], $pkg->getExtensions());
    }

    /**
     * @dataProvider getCreateMethod
     *
     * @param string $method
     */
    public function testCreateWithReplaceDefault($method)
    {
        $config = [
            'name' => 'package1',
            'source_path' => 'SOURCE_PATH',
            'source_base' => 'SOURCE_BASE',
            'extensions' => [
                'js' => [],
            ],
            'replace_default_extensions' => true,
            'patterns' => [
                '*/pattern/*',
            ],
            'replace_default_patterns' => true,
        ];
        $defaultExts = [
            'css' => new FileExtension('css'),
        ];
        $defaultPatterns = [
            '*/default/*',
        ];

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
        $this->assertEquals([
            'js' => new FileExtension('js'),
        ], $pkg->getExtensions());
    }

    public function testConvertToArraySimple()
    {
        $pkg = new ConfigPackage('NAME');
        $config = $ext = PackageFactory::convertToArray($pkg);
        $valid = [
            'name' => 'NAME',
        ];

        $this->assertInternalType('array', $config);
        $this->assertEquals($valid, $config);
    }

    public function testConvertToArraySimpleWithAllFields()
    {
        $pkg = new ConfigPackage('NAME', 'SOURCE_PATH', 'SOURCE_BASE');
        $config = $ext = PackageFactory::convertToArray($pkg, true);
        $valid = [
            'name' => 'NAME',
            'source_path' => 'SOURCE_PATH',
            'source_base' => 'SOURCE_BASE',
            'replace_default_extensions' => false,
            'replace_default_patterns' => false,
            'extensions' => [],
            'patterns' => [],
        ];

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
        $valid = [
            'name' => 'NAME',
            'source_path' => 'SOURCE_PATH',
            'source_base' => 'SOURCE_BASE',
            'replace_default_extensions' => true,
            'replace_default_patterns' => true,
            'extensions' => [
                'js' => [
                    'name' => 'js',
                ],
            ],
        ];

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
        $valid = [
            'name' => 'NAME',
            'source_path' => 'SOURCE_PATH',
            'source_base' => 'SOURCE_BASE',
            'replace_default_extensions' => true,
            'replace_default_patterns' => true,
            'extensions' => [
                'js' => [
                    'name' => 'js',
                ],
            ],
            'patterns' => [],
        ];

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

        $pkg = PackageFactory::merge([$pkg1, $pkg2]);

        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\ConfigPackageInterface', $pkg);
        $this->assertSame('NAME', $pkg->getName());
        $this->assertSame('SOURCE_PATH', $pkg->getSourcePath());
        $this->assertSame('SOURCE_BASE', $pkg->getSourceBase());
        $this->assertTrue($pkg->replaceDefaultExtensions());
        $this->assertTrue($pkg->replaceDefaultPatterns());
        $this->assertEquals([
            'js' => new FileExtension('js'),
            'css' => new FileExtension('css'),
        ], $pkg->getExtensions());
        $this->assertEquals([], $pkg->getPatterns());
    }
}
