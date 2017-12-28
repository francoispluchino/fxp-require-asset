<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Assetic\Config;

use Fxp\Component\RequireAsset\Assetic\Config\OutputManager;
use PHPUnit\Framework\TestCase;

/**
 * Output Manager Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class OutputManagerTest extends TestCase
{
    public function testBasic()
    {
        $om = new OutputManager('assets-test');

        $om->addOutputPattern('inpattern1', 'outpattern1');
        $om->addOutputPatterns([
            'inpattern2' => 'outpattern2',
            'inpattern3' => 'outpattern3',
        ]);

        $this->assertTrue($om->hasOutputPattern('inpattern1'));
        $this->assertTrue($om->hasOutputPattern('inpattern2'));
        $this->assertTrue($om->hasOutputPattern('inpattern3'));
        $this->assertFalse($om->hasOutputPattern('nonexistingpattern'));

        $om->removeOutputPattern('inpattern2');

        $this->assertTrue($om->hasOutputPattern('inpattern1'));
        $this->assertFalse($om->hasOutputPattern('inpattern2'));
        $this->assertTrue($om->hasOutputPattern('inpattern3'));

        $valid = [
            'inpattern1' => 'outpattern1',
            'inpattern3' => 'outpattern3',
        ];
        $this->assertSame($valid, $om->getOutputPatterns());
    }

    public function getDataForConvertOutput()
    {
        return [
            ['package/asset.js', 'assets-test/package/asset.js', [
            ]],
            ['package/asset.js', 'assets-test/package/asset.js', [
                '*' => '*',
            ]],
            ['package/asset.js', 'assets-test/asset.js', [
                'package/*' => '*',
            ]],
            ['package/less/asset.css', 'assets-test/package/css/asset.css', [
                '*/less/*' => '*/css/*',
            ]],
            ['package/less/asset.css', 'assets-test/css/asset.css', [
                '*/less/*' => '*/css/*',
                'package/css/*' => 'css/*',
            ]],
            ['package/fonts/font-name-regular.ttf', 'assets-test/fonts/font-name.ttf', [
                'package/fonts/font-name-regular.*' => 'fonts/font-name.*',
            ]],
            ['package/asset/asset-file.css', 'assets-test/css/asset-file.css', [
                'package/asset/*.css' => 'css/$0.css',
            ]],
            ['package/asset/asset-file.css', 'assets-test/css/asset-file.css', [
                'package/*/*.css' => 'css/$1.css',
            ]],
        ];
    }

    /**
     * @dataProvider getDataForConvertOutput
     *
     * @param string $assert
     * @param string $validOutput
     * @param array  $patterns
     */
    public function testConvertOutput($assert, $validOutput, array $patterns)
    {
        $om = new OutputManager('assets-test');
        $om->addOutputPatterns($patterns);

        $this->assertSame($validOutput, $om->convertOutput($assert));
    }
}
