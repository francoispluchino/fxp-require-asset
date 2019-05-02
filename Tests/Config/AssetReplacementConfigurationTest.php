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
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

/**
 * Asset Replacement Configuration Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 *
 * @internal
 */
final class AssetReplacementConfigurationTest extends TestCase
{
    public function testLocale(): void
    {
        $process = new Processor();
        $configs = [
            [
                '@asset1/path1.ext' => '@asset5/path1.ext',
            ],
            [
                '@asset1/path2.ext' => '@asset5/path2.ext',
                '@asset2/path1.ext' => '@asset5/path3.ext',
            ],
            [
                '@asset1/path1.ext' => '@asset7/path1.ext',
            ],
        ];
        $validConfig = [
            '@asset1/path1.ext' => '@asset7/path1.ext',
            '@asset1/path2.ext' => '@asset5/path2.ext',
            '@asset2/path1.ext' => '@asset5/path3.ext',
        ];

        $res = $process->process(AssetReplacementConfiguration::getNode(), $configs);

        $this->assertSame($validConfig, $res);
    }
}
