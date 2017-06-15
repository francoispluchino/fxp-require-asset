<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Assetic;

use Assetic\Asset\AssetInterface;
use Assetic\Factory\LazyAssetManager;
use Fxp\Component\RequireAsset\Assetic\AsseticRequireAssetManager;
use PHPUnit\Framework\TestCase;

/**
 * Assetic Require Asset Manager Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AsseticRequireAssetManagerTest extends TestCase
{
    /**
     * @var LazyAssetManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $lam;

    /**
     * @var AsseticRequireAssetManager
     */
    protected $ram;

    protected function setUp()
    {
        $this->lam = $this->getMockBuilder(LazyAssetManager::class)->disableOriginalConstructor()->getMock();
        $this->ram = new AsseticRequireAssetManager($this->lam);
    }

    protected function tearDown()
    {
        $this->lam = null;
        $this->ram = null;
    }

    public function testHas()
    {
        $asset = '@package/asset.ext';
        $expectedName = 'package_asset_ext';
        $expectedResult = true;

        $this->lam->expects($this->once())
            ->method('has')
            ->with($expectedName)
            ->willReturn($expectedResult);

        $res = $this->ram->has($asset);

        $this->assertSame($expectedResult, $res);
    }

    public function testGet()
    {
        $asset = '@package/asset.ext';
        $asseticAsset = $this->getMockBuilder(AssetInterface::class)->getMock();
        $asseticAssetPath = '_controller/assets/asset.ext';
        $expectedName = 'package_asset_ext';
        $expectedResult = '/assets/asset.ext';

        $asseticAsset->expects($this->once())
            ->method('getTargetPath')
            ->willReturn($asseticAssetPath);

        $this->lam->expects($this->once())
            ->method('has')
            ->with($expectedName)
            ->willReturn(true);

        $this->lam->expects($this->once())
            ->method('get')
            ->with($expectedName)
            ->willReturn($asseticAsset);

        $res = $this->ram->getPath($asset);

        $this->assertSame($expectedResult, $res);
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\AssetNotFoundException
     * @expectedExceptionMessage The asset "@package/asset.ext" is not found
     */
    public function testGetWithoutAsset()
    {
        $asset = '@package/asset.ext';
        $expectedName = 'package_asset_ext';

        $this->lam->expects($this->once())
            ->method('has')
            ->with($expectedName)
            ->willReturn(false);

        $this->ram->getPath($asset);
    }
}
