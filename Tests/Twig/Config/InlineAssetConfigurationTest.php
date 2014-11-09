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

use Fxp\Component\RequireAsset\Twig\Config\InlineAssetConfiguration;
use Symfony\Component\Config\Definition\Processor;

/**
 * Twig inline asset Configuration Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InlineAssetConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testInlineAsset()
    {
        $process = new Processor();
        $configs = array(
            array(
                'position' => 'head',
            ),
            array(
                'keep_html_tag' => false,
            ),
        );
        $validConfig = array(
            'position'      => 'head',
            'keep_html_tag' => false,
        );

        $res = $process->process(InlineAssetConfiguration::getNode(), $configs);

        $this->assertSame($validConfig, $res);
    }
}
