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
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManager;

/**
 * File Extension Manager Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FileExtensionManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $fem = new FileExtensionManager();

        $fem->addDefaultExtension('ext1');
        $fem->addDefaultExtensions(array(
            'ext2',
            'ext3',
        ));

        $this->assertTrue($fem->hasDefaultExtension('ext1'));
        $this->assertTrue($fem->hasDefaultExtension('ext2'));
        $this->assertTrue($fem->hasDefaultExtension('ext3'));
        $this->assertFalse($fem->hasDefaultExtension('nonexistingextension'));

        $fem->removeDefaultExtension('ext2');

        $this->assertTrue($fem->hasDefaultExtension('ext1'));
        $this->assertFalse($fem->hasDefaultExtension('ext2'));
        $this->assertTrue($fem->hasDefaultExtension('ext3'));

        $valid = array(
            'ext1' => new FileExtension('ext1'),
            'ext3' => new FileExtension('ext3'),
        );
        $this->assertEquals($valid, $fem->getDefaultExtensions());
        $this->assertEquals($valid['ext1'], $fem->getDefaultExtension('ext1'));
        $this->assertEquals($valid['ext3'], $fem->getDefaultExtension('ext3'));
    }

    public function testAddDefaultFileExtensionsWithArrayConfig()
    {
        $fem = new FileExtensionManager();

        $fem->addDefaultExtensions(array(
            'ext1' => array(),
            'ext2' => array(),
        ));

        $valid = array(
            'ext1' => new FileExtension('ext1'),
            'ext2' => new FileExtension('ext2'),
        );
        $this->assertEquals($valid, $fem->getDefaultExtensions());
    }

    public function testAddDefaultFileExtensionWithLockedManager()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\BadMethodCallException');

        $fem = new FileExtensionManager();
        $this->assertSame(array(), $fem->getDefaultExtensions());

        $fem->addDefaultExtension('fileextension');
    }

    public function testAddDefaultFileExtensionsWithLockedManager()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\BadMethodCallException');

        $fem = new FileExtensionManager();
        $this->assertSame(array(), $fem->getDefaultExtensions());

        $fem->addDefaultExtensions(array('fileextension'));
    }

    public function testRemoveDefaultFileExtensionWithLockedManager()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\BadMethodCallException');

        $fem = new FileExtensionManager();
        $this->assertSame(array(), $fem->getDefaultExtensions());

        $fem->removeDefaultExtension('fileextension');
    }

    public function testGetDefaultFileExtensionWithNonexistingFileExtension()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\InvalidConfigurationException');

        $fem = new FileExtensionManager();
        $fem->getDefaultExtension('fileextension');
    }
}
