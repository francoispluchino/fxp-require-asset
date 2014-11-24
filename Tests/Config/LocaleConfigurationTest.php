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
use Symfony\Component\Config\Definition\Processor;

/**
 * Locale Configuration Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class LocaleConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testLocale()
    {
        $process = new Processor();
        $configs = array(
            array(
                'en_US' => array(
                    '@asset/source/path.ext' => array(
                        '@asset/source/locale/path-en-us.ext',
                    ),
                ),
                'fr' => array(
                    '@asset/source/path.ext' => '@asset/source/locale/path-fr.ext',
                ),
            ),
            array(
                'en' => array(
                    '@asset/source/path.ext' => '@asset/source/locale/path-en.ext',
                ),
            ),
        );
        $validConfig = array(
            'en_US' => array(
                '@asset/source/path.ext' => array(
                    '@asset/source/locale/path-en-us.ext',
                ),
            ),
            'fr' => array(
                '@asset/source/path.ext' => array(
                    '@asset/source/locale/path-fr.ext',
                ),
            ),
            'en' => array(
                '@asset/source/path.ext' => array(
                    '@asset/source/locale/path-en.ext',
                ),
            ),
        );

        $res = $process->process(LocaleConfiguration::getNode(), $configs);

        $this->assertSame($validConfig, $res);
    }
}
