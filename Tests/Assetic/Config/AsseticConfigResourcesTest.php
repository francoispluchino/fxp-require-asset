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

use Fxp\Component\RequireAsset\Assetic\Config\AsseticConfigResources;
use Fxp\Component\RequireAsset\Assetic\Util\AssetResourceUtils;

/**
 * Assetic Config Resources Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AsseticConfigResourcesTest extends \PHPUnit_Framework_TestCase
{
    public function testGetResource()
    {
        $configs = new AsseticConfigResources();
        $name = '@asset/path.ext';
        $classname = 'Fxp\Component\RequireAsset\Assetic\Factory\Resource\CommonRequireAssetResource';
        $inputs = array($name);
        $targetPath = 'target.ext';
        $filters = array();
        $options = array();
        $args = array($name, $inputs, $targetPath, $filters, $options);

        $resource = AssetResourceUtils::createAssetResource($name, $classname, $args, 0);

        $this->assertFalse($configs->hasResource($name));
        $configs->addResource($resource);
        $this->assertTrue($configs->hasResource($name));
        $this->assertSame($resource, $configs->getResource($name));
    }

    public function testGetResourceWithInvalidResourceName()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\InvalidArgumentException', 'The "@asset/path.ext" config of asset resource does not exist');

        $configs = new AsseticConfigResources();
        $configs->getResource('@asset/path.ext');
    }
}
