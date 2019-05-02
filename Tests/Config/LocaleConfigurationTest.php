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

use Fxp\Component\RequireAsset\Config\LocaleConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

/**
 * Locale Configuration Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 *
 * @internal
 */
final class LocaleConfigurationTest extends TestCase
{
    public function testLocale(): void
    {
        $process = new Processor();
        $configs = [
            [
                'en_US' => [
                    '@asset/source/path.ext' => [
                        '@asset/source/locale/path-en-us.ext',
                    ],
                ],
                'fr' => [
                    '@asset/source/path.ext' => '@asset/source/locale/path-fr.ext',
                ],
            ],
            [
                'en' => [
                    '@asset/source/path.ext' => '@asset/source/locale/path-en.ext',
                ],
            ],
        ];
        $validConfig = [
            'en_US' => [
                '@asset/source/path.ext' => [
                    '@asset/source/locale/path-en-us.ext',
                ],
            ],
            'fr' => [
                '@asset/source/path.ext' => [
                    '@asset/source/locale/path-fr.ext',
                ],
            ],
            'en' => [
                '@asset/source/path.ext' => [
                    '@asset/source/locale/path-en.ext',
                ],
            ],
        ];

        $res = $process->process(LocaleConfiguration::getNode(), $configs);

        $this->assertSame($validConfig, $res);
    }
}
