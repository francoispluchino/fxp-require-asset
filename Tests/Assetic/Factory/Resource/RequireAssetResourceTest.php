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
use PHPUnit\Framework\TestCase;

/**
 * Require Asset Resource Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetResourceTest extends TestCase
{
    public function testRequireAssetResource()
    {
        $time = new \DateTime();
        $formulaeName = 'foobar_js';
        $name = 'foobar.js';
        $sourcePath = 'PATH/foobar.js';
        $targetPath = 'output/foobar.js';
        $filters = ['?yui_js'];
        $options = [];
        $resource = new RequireAssetResource($name, $sourcePath, $targetPath, $filters, $options);

        $this->assertSame($formulaeName, (string) $resource);
        $this->assertSame($name, $resource->getPrettyName());
        $this->assertSame($sourcePath, $resource->getSourcePath());
        $this->assertTrue($resource->isFresh($time->getTimestamp()));
        $this->assertSame('', $resource->getContent());

        $validFormulae = [
            $formulaeName => [
                [$sourcePath],
                $filters,
                array_merge($options, [
                    'debug' => false,
                    'output' => $targetPath,
                ]),
            ],
        ];
        $this->assertSame($validFormulae, $resource->getFormulae());
    }
}
