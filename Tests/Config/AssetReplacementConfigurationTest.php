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

use Fxp\Component\RequireAsset\Config\AssetReplacementConfiguration;
use Symfony\Component\Config\Definition\Processor;

/**
 * Asset Replacement Configuration Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetReplacementConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testLocale()
    {
        $process = new Processor();
        $configs = array(
            array(
                '@asset1/path1.ext' => '@asset5/path1.ext',
            ),
            array(
                '@asset1/path2.ext' => '@asset5/path2.ext',
                '@asset2/path1.ext' => '@asset5/path3.ext',
            ),
            array(
                '@asset1/path1.ext' => '@asset7/path1.ext',
            ),
        );
        $validConfig = array(
            '@asset1/path1.ext' => '@asset7/path1.ext',
            '@asset1/path2.ext' => '@asset5/path2.ext',
            '@asset2/path1.ext' => '@asset5/path3.ext',
        );

        $res = $process->process(AssetReplacementConfiguration::getNode(), $configs);

        $this->assertSame($validConfig, $res);
    }
}
