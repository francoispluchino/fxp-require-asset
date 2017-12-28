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

use Fxp\Component\RequireAsset\Config\PackageConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

/**
 * Package Configuration Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class PackageConfigurationTest extends TestCase
{
    public function testFileExtension()
    {
        $process = new Processor();
        $configs = [
            [
                'package1' => [
                    'source_path' => 'PACKAGE1_PATH',
                ],
                'package2' => [
                    'source_path' => 'PACKAGE2_PATH',
                    'extensions' => [
                        'js' => [],
                        'less' => [
                            'extension' => 'css',
                        ],
                    ],
                ],
            ],
            [
                'package2' => [
                    'extensions' => [
                        'less' => [
                            'filters' => ['lessphp'],
                        ],
                    ],
                ],
            ],
        ];
        $validConfig = [
            'package1' => [
                'source_path' => 'PACKAGE1_PATH',
                'source_base' => null,
                'replace_default_extensions' => false,
                'replace_default_patterns' => false,
                'extensions' => [],
                'patterns' => [],
            ],
            'package2' => [
                'source_path' => 'PACKAGE2_PATH',
                'source_base' => null,
                'replace_default_extensions' => false,
                'replace_default_patterns' => false,
                'extensions' => [
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
                ],
                'patterns' => [],
            ],
        ];

        $res = $process->process(PackageConfiguration::getNode(), $configs);

        $this->assertEquals($validConfig, $res);
    }
}
