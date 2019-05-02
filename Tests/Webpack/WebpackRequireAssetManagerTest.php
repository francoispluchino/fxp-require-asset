<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Webpack;

use Fxp\Component\RequireAsset\Exception\AssetNotFoundException;
use Fxp\Component\RequireAsset\Webpack\Adapter\AdapterInterface;
use Fxp\Component\RequireAsset\Webpack\WebpackRequireAssetManager;
use PHPUnit\Framework\TestCase;

/**
 * Webpack Require Asset Manager Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 *
 * @internal
 */
final class WebpackRequireAssetManagerTest extends TestCase
{
    /**
     * @var AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapter;

    /**
     * @var WebpackRequireAssetManager
     */
    protected $ram;

    protected function setUp(): void
    {
        $this->adapter = $this->getMockBuilder(AdapterInterface::class)->getMock();
        $this->ram = new WebpackRequireAssetManager($this->adapter);
    }

    protected function tearDown(): void
    {
        $this->adapter = null;
        $this->ram = null;
    }

    public function testHas(): void
    {
        $asset = '@webpack/asset_js';

        $this->adapter->expects($this->once())
            ->method('getPath')
            ->with($asset, null)
            ->willReturn('/assets.js')
        ;

        $this->assertTrue($this->ram->has($asset));
    }

    public function testHasNotAsset(): void
    {
        $asset = '@webpack/asset_not_found';

        $this->adapter->expects($this->once())
            ->method('getPath')
            ->with($asset, null)
            ->willThrowException(new AssetNotFoundException('Not Found'))
        ;

        $this->assertFalse($this->ram->has($asset));
    }

    public function testGet(): void
    {
        $asset = '@webpack/asset_js';

        $this->adapter->expects($this->once())
            ->method('getPath')
            ->with($asset, null)
            ->willReturn('/assets.js')
        ;

        $this->assertSame('/assets.js', $this->ram->getPath($asset, null));
    }

    public function testGetNotAsset(): void
    {
        $this->expectException(\Fxp\Component\RequireAsset\Exception\AssetNotFoundException::class);
        $this->expectExceptionMessage('Not Found');

        $asset = '@webpack/asset_not_found';

        $this->adapter->expects($this->once())
            ->method('getPath')
            ->with($asset, null)
            ->willThrowException(new AssetNotFoundException('Not Found'))
        ;

        $this->ram->getPath($asset, null);
    }
}
