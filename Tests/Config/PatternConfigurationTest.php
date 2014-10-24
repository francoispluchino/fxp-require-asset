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

use Fxp\Component\RequireAsset\Config\PatternConfiguration;
use Symfony\Component\Config\Definition\Processor;

/**
 * Pattern Configuration Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class PatternConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testPattern()
    {
        $process = new Processor();
        $configs = array(
            array(
                'js/*',
            ),
            array(
                'css/*',
            ),
        );
        $validConfig = array(
            'js/*',
            'css/*',
        );

        $res = $process->process(PatternConfiguration::getNode(), $configs);

        $this->assertSame($validConfig, $res);
    }
}