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
use Symfony\Component\Config\Definition\Processor;

/**
 * File Extension Configuration Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FileExtensionConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testFileExtension()
    {
        $process = new Processor();
        $configs = array(
            array(
                'js' => array(),
                'less' => array(
                    'extension' => 'css',
                ),
            ),
            array(
                'less' => array(
                    'filters' => array('lessphp'),
                ),
            ),
        );
        $validConfig = array(
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
        );

        $res = $process->process(FileExtensionConfiguration::getNode(), $configs);

        $this->assertEquals($validConfig, $res);
    }
}
