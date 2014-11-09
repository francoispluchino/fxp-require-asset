<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Twig\Config;

use Fxp\Component\RequireAsset\Twig\Config\RequireStyleConfiguration;
use Symfony\Component\Config\Definition\Processor;

/**
 * Twig require script Configuration Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireStyleConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testRequireStyle()
    {
        $process = new Processor();
        $configs = array(
            array(
                'position' => 'head',
            ),
            array(
                'media' => 'all',
                'hreflang' => 'en',
            ),
        );
        $validConfig = array(
            'position' => 'head',
            'media'    => 'all',
            'hreflang' => 'en',
            'href'     => null,
            'rel'      => 'stylesheet',
            'type'     => null,
            'sizes'    => null,
        );

        $res = $process->process(RequireStyleConfiguration::getNode(), $configs);

        $this->assertSame($validConfig, $res);
    }
}
