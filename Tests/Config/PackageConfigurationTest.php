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
use Symfony\Component\Config\Definition\Processor;

/**
 * Package Configuration Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class PackageConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testFileExtension()
    {
        $process = new Processor();
        $configs = array(
            array(
                'package1' => array(
                    'source_path' => 'PACKAGE1_PATH',
                ),
                'package2' => array(
                    'source_path' => 'PACKAGE2_PATH',
                    'extensions' => array(
                        'js' => array(),
                        'less' => array(
                            'extension' => 'css',
                        ),
                    ),
                ),
            ),
            array(
                'package2' => array(
                    'extensions' => array(
                        'less' => array(
                            'filters' => array('lessphp'),
                        ),
                    ),
                ),
            ),
        );
        $validConfig = array(
            'package1' => array(
                'source_path' => 'PACKAGE1_PATH',
                'source_base' => null,
                'replace_default_extensions' => false,
                'replace_default_patterns' => false,
                'extensions' => array(),
                'patterns' => array(),
            ),
            'package2' => array(
                'source_path' => 'PACKAGE2_PATH',
                'source_base' => null,
                'replace_default_extensions' => false,
                'replace_default_patterns' => false,
                'extensions' => array(
                    'js' => array(
                        'filters' => array(),
                        'options' => array(),
                        'extension' => null,
                        'debug' => false,
                        'exclude' => false,
                    ),
                    'less' => array(
                        'filters' => array('lessphp'),
                        'options' => array(),
                        'extension' => 'css',
                        'debug' => false,
                        'exclude' => false,
                    ),
                ),
                'patterns' => array(),
            ),
        );

        $res = $process->process(PackageConfiguration::getNode(), $configs);

        $this->assertEquals($validConfig, $res);
    }
}
