<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Config;

use Fxp\Component\RequireAsset\Config\FileExtensionConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

/**
 * File Extension Configuration Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FileExtensionConfigurationTest extends TestCase
{
    public function testFileExtension()
    {
        $process = new Processor();
        $configs = [
            [
                'js' => [],
                'less' => [
                    'extension' => 'css',
                ],
            ],
            [
                'less' => [
                    'filters' => ['lessphp'],
                ],
            ],
        ];
        $validConfig = [
            'js' => [
                'filters' => [],
                'options' => [],
                'extension' => null,
                'debug' => false,
                'exclude' => false,
            ],
            'less' => [
                'filters' => ['lessphp'],
                'options' => [],
                'extension' => 'css',
                'debug' => false,
                'exclude' => false,
            ],
        ];

        $res = $process->process(FileExtensionConfiguration::getNode(), $configs);

        $this->assertEquals($validConfig, $res);
    }
}
