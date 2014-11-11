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
use Symfony\Component\Config\Definition\Processor;

/**
 * Tequire script template tag configuration tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireStyleTagConfigurationTest extends \PHPUnit_Framework_TestCase
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

        $res = $process->process(RequireStyleTagConfiguration::getNode(), $configs);

        $this->assertSame($validConfig, $res);
    }
}
