<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Tag\Config;

use Fxp\Component\RequireAsset\Tag\Config\RequireStyleTagConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

/**
 * Tequire script template tag configuration tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 *
 * @internal
 */
final class RequireStyleTagConfigurationTest extends TestCase
{
    public function testRequireStyle(): void
    {
        $process = new Processor();
        $configs = [
            [
                'position' => 'head',
            ],
            [
                'media' => 'all',
                'hreflang' => 'en',
            ],
        ];
        $validConfig = [
            'position' => 'head',
            'media' => 'all',
            'hreflang' => 'en',
            'href' => null,
            'rel' => 'stylesheet',
            'type' => null,
            'sizes' => null,
        ];

        $res = $process->process(RequireStyleTagConfiguration::getNode(), $configs);

        $this->assertSame($validConfig, $res);
    }
}
