<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Assetic\Util;

use Fxp\Component\RequireAsset\Assetic\Util\FileExtensionUtils;
use PHPUnit\Framework\TestCase;

/**
 * File Extension Utils Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FileExtensionUtilsTest extends TestCase
{
    public function testCreateByConfig()
    {
        $name = 'fileextension';
        $options = array();
        $filters = array();
        $extension = 'customname';
        $debug = true;
        $exclude = true;

        $fe = FileExtensionUtils::createByConfig($name, $options, $filters, $extension, $debug, $exclude);

        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\FileExtensionInterface', $fe);
        $this->assertSame($name, $fe->getName());
        $this->assertSame($options, $fe->getOptions());
        $this->assertSame($filters, $fe->getFilters());
        $this->assertSame($extension, $fe->getOutputExtension());
        $this->assertSame($debug, $fe->isDebug());
        $this->assertSame($exclude, $fe->isExclude());
    }

    public function testGetDefaultConfigs()
    {
        $configs = FileExtensionUtils::getDefaultConfigs();

        $this->assertInternalType('array', $configs);
    }
}
