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

/**
 * File Extension Factory Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FileExtensionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateWhithoutName()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\InvalidArgumentException');

        FileExtensionFactory::create(array());
    }

    public function testCreate()
    {
        $config = array(
            'name' => 'less',
            'options' => array(),
            'filters' => array('lessphp'),
            'extension' => 'css',
            'debug' => true,
            'exclude' => true,
        );
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
        $valid = array(
            'name' => 'NAME',
        );

        $this->assertTrue(is_array($config));
        $this->assertEquals($valid, $config);
    }

    public function testConvertToArraySimpleWithAllFields()
    {
        $ext = new FileExtension('NAME');
        $config = $ext = FileExtensionFactory::convertToArray($ext, true);
        $valid = array(
            'name'      => 'NAME',
            'options'   => array(),
            'filters'   => array(),
            'extension' => null,
            'debug'     => false,
            'exclude'   => false,
        );

        $this->assertTrue(is_array($config));
        $this->assertEquals($valid, $config);
    }

    public function testConvertToArrayComplexe()
    {
        $ext = new FileExtension('NAME', array(), array('filter'), 'ext', false, true);
        $config = $ext = FileExtensionFactory::convertToArray($ext);
        $valid = array(
            'name'      => 'NAME',
            'filters'   => array('filter'),
            'extension' => 'ext',
            'exclude'   => true,
        );

        $this->assertTrue(is_array($config));
        $this->assertEquals($valid, $config);
    }

    public function testConvertToArrayComplexeWithAllFields()
    {
        $ext = new FileExtension('NAME', array(), array('filter'), 'ext', false, true);
        $config = $ext = FileExtensionFactory::convertToArray($ext, true);
        $valid = array(
            'name'      => 'NAME',
            'options'   => array(),
            'filters'   => array('filter'),
            'extension' => 'ext',
            'debug'     => false,
            'exclude'   => true,
        );

        $this->assertTrue(is_array($config));
        $this->assertEquals($valid, $config);
    }

    public function testMerge()
    {
        $ext1 = new FileExtension('NAME', array(), array('filter1'), null, false, true);
        $ext2 = new FileExtension('NAME', array('option2'), array('filter2'), 'ext', true);
        $ext = FileExtensionFactory::merge(array($ext1, $ext2));

        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\FileExtensionInterface', $ext);
        $this->assertSame('NAME', $ext->getName());
        $this->assertSame(array('option2'), $ext->getOptions());
        $this->assertSame(array('filter1', 'filter2'), $ext->getFilters());
        $this->assertSame('ext', $ext->getOutputExtension());
        $this->assertTrue($ext->isDebug());
        $this->assertTrue($ext->isExclude());
    }
}
