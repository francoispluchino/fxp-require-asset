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

use Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResource;

/**
 * Require Asset Resource Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetResourceTest extends \PHPUnit_Framework_TestCase
{
    public function testRequireAssetResource()
    {
        $time = new \DateTime();
        $formulaeName = 'foobar_js';
        $name = 'foobar.js';
        $sourcePath = 'PATH/foobar.js';
        $targetPath = 'output/foobar.js';
        $filters = array('?yui_js');
        $options = array();
        $resource = new RequireAssetResource($name, $sourcePath, $targetPath, $filters, $options);

        $this->assertSame('require_asset_resource_' . $formulaeName, (string) $resource);
        $this->assertSame($name, $resource->getName());
        $this->assertSame($sourcePath, $resource->getSourcePath());
        $this->assertTrue($resource->isFresh($time->getTimestamp()));
        $this->assertSame('', $resource->getContent());

        $validFormulae = array(
            $formulaeName => array(
                array($sourcePath),
                $filters,
                array_merge($options, array(
                    'output' => $targetPath,
                    'debug' => false,
                )),
            ),
        );
        $this->assertSame($validFormulae, $resource->getFormulae());
    }
}
