<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Assetic\Factory\Resource;

use Fxp\Component\RequireAsset\Assetic\Factory\Resource\CommonRequireAssetResource;
use PHPUnit\Framework\TestCase;

/**
 * Common Require Asset Resource Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class CommonRequireAssetResourceTest extends TestCase
{
    public function testCommonRequireAssetResource()
    {
        $time = new \DateTime();
        $formulaeName = 'foobar_js';
        $name = 'foobar.js';
        $inputs = array('@asset/foobar/foobar.js');
        $targetPath = 'output/foobar.js';
        $filters = array('?yui_js');
        $options = array();
        $resource = new CommonRequireAssetResource($name, $inputs, $targetPath, $filters, $options);

        $this->assertSame($formulaeName, (string) $resource);
        $this->assertSame($name, $resource->getPrettyName());
        $this->assertTrue($resource->isFresh($time->getTimestamp()));
        $this->assertSame('', $resource->getContent());

        $validFormulae = array(
            $formulaeName => array(
                array('@asset_foobar_foobar_js'),
                $filters,
                array_merge($options, array(
                    'fxp_require_common_asset' => true,
                    'output' => $targetPath,
                )),
            ),
        );
        $this->assertSame($validFormulae, $resource->getFormulae());
    }
}
