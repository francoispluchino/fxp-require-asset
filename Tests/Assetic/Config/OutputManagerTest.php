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

/**
 * Output Manager Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class OutputManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $om = new OutputManager('assets-test');

        $om->addOutputPattern('inpattern1', 'outpattern1');
        $om->addOutputPatterns(array(
            'inpattern2' => 'outpattern2',
            'inpattern3' => 'outpattern3',
        ));

        $this->assertTrue($om->hasOutputPattern('inpattern1'));
        $this->assertTrue($om->hasOutputPattern('inpattern2'));
        $this->assertTrue($om->hasOutputPattern('inpattern3'));
        $this->assertFalse($om->hasOutputPattern('nonexistingpattern'));

        $om->removeOutputPattern('inpattern2');

        $this->assertTrue($om->hasOutputPattern('inpattern1'));
        $this->assertFalse($om->hasOutputPattern('inpattern2'));
        $this->assertTrue($om->hasOutputPattern('inpattern3'));

        $valid = array(
            'inpattern1' => 'outpattern1',
            'inpattern3' => 'outpattern3',
        );
        $this->assertSame($valid, $om->getOutputPatterns());
    }

    public function getDataForConvertOutput()
    {
        return array(
            array('package/asset.js', 'assets-test/package/asset.js', array(
            )),
            array('package/asset.js', 'assets-test/package/asset.js', array(
                '*' => '*',
            )),
            array('package/asset.js', 'assets-test/asset.js', array(
                'package/*' => '*',
            )),
            array('package/less/asset.css', 'assets-test/package/css/asset.css', array(
                '*/less/*' => '*/css/*',
            )),
            array('package/less/asset.css', 'assets-test/css/asset.css', array(
                '*/less/*' => '*/css/*',
                'package/css/*' => 'css/*',
            )),
            array('package/fonts/font-name-regular.ttf', 'assets-test/fonts/font-name.ttf', array(
                'package/fonts/font-name-regular.*' => 'fonts/font-name.*',
            )),
            array('package/asset/asset-file.css', 'assets-test/css/asset-file.css', array(
                'package/asset/*.css' => 'css/$0.css',
            )),
            array('package/asset/asset-file.css', 'assets-test/css/asset-file.css', array(
                'package/*/*.css' => 'css/$1.css',
            )),
        );
    }

    /**
     * @dataProvider getDataForConvertOutput
     */
    public function testConvertOutput($assert, $validOutput, array $patterns)
    {
        $om = new OutputManager('assets-test');
        $om->addOutputPatterns($patterns);

        $this->assertSame($validOutput, $om->convertOutput($assert));
    }
}
