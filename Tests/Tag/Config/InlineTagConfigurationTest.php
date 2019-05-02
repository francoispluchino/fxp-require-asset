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

use Fxp\Component\RequireAsset\Tag\Config\InlineTagConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

/**
 * Inline template tag configuration tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 *
 * @internal
 */
final class InlineTagConfigurationTest extends TestCase
{
    public function testInlineAsset(): void
    {
        $process = new Processor();
        $configs = [
            [
                'position' => 'head',
            ],
            [
                'keep_html_tag' => false,
            ],
        ];
        $validConfig = [
            'position' => 'head',
            'keep_html_tag' => false,
        ];

        $res = $process->process(InlineTagConfiguration::getNode(), $configs);

        $this->assertSame($validConfig, $res);
    }
}
