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

use Fxp\Component\RequireAsset\Config\CommonAssetConfiguration;
use Symfony\Component\Config\Definition\Processor;

/**
 * Common Asset Configuration Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class CommonAssetConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testCommonAsset()
    {
        $process = new Processor();
        $configs = array(
             array(
                'common_asset_1' => array(
                    'output' => 'OUTPUT_1.ext',
                    'debug' => true,
                ),
                 'common_asset_2' => array(
                     'output' => 'OUTPUT_2.ext',
                     'inputs' => '@asset/source/path.ext',
                 ),
                 'common_asset_3' => '@asset3/source/path.ext',
            ),
        );
        $validConfig = array(
            'common_asset_1' => array(
                'output' => 'OUTPUT_1.ext',
                'options' => array('debug' => true),
                'inputs' => array(),
                'filters' => array(),
            ),
            'common_asset_2' => array(
                'output' => 'OUTPUT_2.ext',
                'inputs' => array('@asset/source/path.ext'),
                'filters' => array(),
                'options' => array(),
            ),
            'common_asset_3' => array(
                'output' => '/asset3/source/path.ext',
                'inputs' => array('@asset3/source/path.ext'),
                'filters' => array(),
                'options' => array(),
            ),
        );

        $res = $process->process(CommonAssetConfiguration::getNode(), $configs);

        $this->assertSame($validConfig, $res);
    }
}
