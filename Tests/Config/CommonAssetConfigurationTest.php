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
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

/**
 * Common Asset Configuration Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class CommonAssetConfigurationTest extends TestCase
{
    public function testCommonAsset()
    {
        $process = new Processor();
        $configs = [
             [
                'common_asset_1' => [
                    'output' => 'OUTPUT_1.ext',
                    'debug' => true,
                ],
                 'common_asset_2' => [
                     'output' => 'OUTPUT_2.ext',
                     'inputs' => '@asset/source/path.ext',
                 ],
                 'common_asset_3' => '@asset3/source/path.ext',
            ],
        ];
        $validConfig = [
            'common_asset_1' => [
                'output' => 'OUTPUT_1.ext',
                'options' => ['debug' => true],
                'inputs' => [],
                'filters' => [],
            ],
            'common_asset_2' => [
                'output' => 'OUTPUT_2.ext',
                'inputs' => ['@asset/source/path.ext'],
                'filters' => [],
                'options' => [],
            ],
            'common_asset_3' => [
                'output' => '/asset3/source/path.ext',
                'inputs' => ['@asset3/source/path.ext'],
                'filters' => [],
                'options' => [],
            ],
        ];

        $res = $process->process(CommonAssetConfiguration::getNode(), $configs);

        $this->assertSame($validConfig, $res);
    }
}
