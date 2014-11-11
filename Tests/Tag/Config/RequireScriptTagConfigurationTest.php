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

use Fxp\Component\RequireAsset\Tag\Config\RequireScriptTagConfiguration;
use Symfony\Component\Config\Definition\Processor;

/**
 * Require script template tag configuration tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireScriptTagConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testRequireScript()
    {
        $process = new Processor();
        $configs = array(
            array(
                'position' => 'head',
            ),
            array(
                'async' => true,
                'defer' => false,
            ),
        );
        $validConfig = array(
            'position' => 'head',
            'async'    => 'async',
            'defer'    => null,
            'src'      => null,
            'charset'  => null,
            'type'     => null,
        );

        $res = $process->process(RequireScriptTagConfiguration::getNode(), $configs);

        $this->assertSame($validConfig, $res);
    }
}
