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

use Fxp\Component\RequireAsset\Assetic\Config\FileExtension;
use Fxp\Component\RequireAsset\Assetic\Factory\Config\FileExtensionFactory;
use PHPUnit\Framework\TestCase;

/**
 * File Extension Factory Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FileExtensionFactoryTest extends TestCase
{
    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\InvalidArgumentException
     */
    public function testCreateWhithoutName()
    {
        FileExtensionFactory::create([]);
    }

    public function testCreate()
    {
        $config = [
            'name' => 'less',
            'options' => [],
            'filters' => ['lessphp'],
            'extension' => 'css',
            'debug' => true,
            'exclude' => true,
        ];
        $ext = FileExtensionFactory::create($config);

        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\FileExtensionInterface', $ext);
        $this->assertSame($config['name'], $ext->getName());
        $this->assertSame($config['options'], $ext->getOptions());
        $this->assertSame($config['filters'], $ext->getFilters());
        $this->assertSame($config['extension'], $ext->getOutputExtension());
        $this->assertSame($config['debug'], $ext->isDebug());
        $this->assertSame($config['exclude'], $ext->isExclude());
    }

    public function testConvertToArraySimple()
    {
        $ext = new FileExtension('NAME');
        $config = $ext = FileExtensionFactory::convertToArray($ext);
        $valid = [
            'name' => 'NAME',
        ];

        $this->assertInternalType('array', $config);
        $this->assertEquals($valid, $config);
    }

    public function testConvertToArraySimpleWithAllFields()
    {
        $ext = new FileExtension('NAME');
        $config = $ext = FileExtensionFactory::convertToArray($ext, true);
        $valid = [
            'name' => 'NAME',
            'options' => [],
            'filters' => [],
            'extension' => null,
            'debug' => false,
            'exclude' => false,
        ];

        $this->assertInternalType('array', $config);
        $this->assertEquals($valid, $config);
    }

    public function testConvertToArrayComplexe()
    {
        $ext = new FileExtension('NAME', [], ['filter'], 'ext', false, true);
        $config = $ext = FileExtensionFactory::convertToArray($ext);
        $valid = [
            'name' => 'NAME',
            'filters' => ['filter'],
            'extension' => 'ext',
            'exclude' => true,
        ];

        $this->assertInternalType('array', $config);
        $this->assertEquals($valid, $config);
    }

    public function testConvertToArrayComplexeWithAllFields()
    {
        $ext = new FileExtension('NAME', [], ['filter'], 'ext', false, true);
        $config = $ext = FileExtensionFactory::convertToArray($ext, true);
        $valid = [
            'name' => 'NAME',
            'options' => [],
            'filters' => ['filter'],
            'extension' => 'ext',
            'debug' => false,
            'exclude' => true,
        ];

        $this->assertInternalType('array', $config);
        $this->assertEquals($valid, $config);
    }

    public function testMerge()
    {
        $ext1 = new FileExtension('NAME', [], ['filter1'], null, false, true);
        $ext2 = new FileExtension('NAME', ['option2'], ['filter2'], 'ext', true);
        $ext = FileExtensionFactory::merge([$ext1, $ext2]);

        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\FileExtensionInterface', $ext);
        $this->assertSame('NAME', $ext->getName());
        $this->assertSame(['option2'], $ext->getOptions());
        $this->assertSame(['filter1', 'filter2'], $ext->getFilters());
        $this->assertSame('ext', $ext->getOutputExtension());
        $this->assertTrue($ext->isDebug());
        $this->assertTrue($ext->isExclude());
    }
}
