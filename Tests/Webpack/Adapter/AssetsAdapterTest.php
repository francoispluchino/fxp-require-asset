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

use Fxp\Component\RequireAsset\Webpack\Adapter\AssetsAdapter;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Webpack Assets Adapter Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetsAdapterTest extends TestCase
{
    /**
     * @var AssetsAdapter
     */
    protected $adapter;

    protected function setUp()
    {
        $this->adapter = new AssetsAdapter(
            realpath(__DIR__.'/../../Fixtures/Webpack/assets.json')
        );
    }

    protected function tearDown()
    {
        $this->adapter = null;
    }

    public function getPathValues()
    {
        return [
            ['@webpack/asset', 'js', '/assets/asset.js'],
            ['@webpack/asset', 'script', '/assets/asset.js'],
            ['@webpack/asset', 'css', '/assets/asset.css'],
            ['@webpack/asset', 'style', '/assets/asset.css'],
            ['@webpack/asset_js', null, '/assets/asset_js.js'],
            ['@webpack/asset_css', null, '/assets/asset_css.css'],
            ['@webpack/asset_ext', null, '/assets/asset_ext.ext'],
        ];
    }

    /**
     * @dataProvider getPathValues
     *
     * @param string      $asset
     * @param string|null $type
     * @param string      $expectedResult
     */
    public function testGet($asset, $type, $expectedResult)
    {
        $res = $this->adapter->getPath($asset, $type);

        $this->assertSame($expectedResult, $res);
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\InvalidArgumentException
     * @expectedExceptionMessage The asset type is required for the asset "@webpack/asset_ext2"
     */
    public function testGetWithRequireType()
    {
        $this->adapter->getPath('@webpack/asset_ext2');
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\AssetNotFoundException
     * @expectedExceptionMessage The asset "@webpack/asset_not_found" is not found
     */
    public function testGetWithoutAsset()
    {
        $asset = '@webpack/asset_not_found';

        $this->adapter->getPath($asset);
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\InvalidArgumentException
     * @expectedExceptionMessage Cannot access "INVALID_ASSET.json" to read the JSON file
     */
    public function testInvalidJsonFilename()
    {
        $this->adapter = new AssetsAdapter('INVALID_ASSET.json');

        $this->adapter->getPath('@webpack/asset');
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\InvalidArgumentException
     * @expectedExceptionMessage Cannot read the JSON content: Syntax error
     */
    public function testInvalidJsonContent()
    {
        $this->adapter = new AssetsAdapter(
            realpath(__DIR__.'/../../Fixtures/Webpack/assets_invalid.json')
        );

        $this->adapter->getPath('@webpack/asset');
    }

    public function testGetPathWithCache()
    {
        /* @var CacheItemPoolInterface|\PHPUnit_Framework_MockObject_MockObject $cache */
        $cache = $this->getMockBuilder(CacheItemPoolInterface::class)->getMock();
        $cacheKey = 'custom_key';
        $asset = '@webpack/asset_js';
        $expected = '/assets/asset_js.js';
        $assetFile = realpath(__DIR__.'/../../Fixtures/Webpack/assets.json');

        $this->adapter = new AssetsAdapter(
            $assetFile,
            $cache,
            $cacheKey
        );

        $cacheItem = $this->getMockBuilder(CacheItemInterface::class)->getMock();

        $cacheItem->expects($this->once())
            ->method('get')
            ->willReturn(json_decode(file_get_contents($assetFile), true));

        $cacheItem->expects($this->once())
            ->method('isHit')
            ->willReturn(true);

        $cache->expects($this->at(0))
            ->method('getItem')
            ->with($cacheKey)
            ->willReturn($cacheItem);

        $res = $this->adapter->getPath($asset);
        $this->assertSame($expected, $res);
    }

    public function testGetPathWithEmptyCache()
    {
        /* @var CacheItemPoolInterface|\PHPUnit_Framework_MockObject_MockObject $cache */
        $cache = $this->getMockBuilder(CacheItemPoolInterface::class)->getMock();
        $cacheKey = 'custom_key';
        $asset = '@webpack/asset_js';
        $expected = '/assets/asset_js.js';
        $assetFile = realpath(__DIR__.'/../../Fixtures/Webpack/assets.json');

        $this->adapter = new AssetsAdapter(
            $assetFile,
            $cache,
            $cacheKey
        );

        $cacheItem = $this->getMockBuilder(CacheItemInterface::class)->getMock();

        $cacheItem->expects($this->once())
            ->method('get')
            ->willReturn(null);

        $cacheItem->expects($this->once())
            ->method('isHit')
            ->willReturn(false);

        $cacheItem->expects($this->once())
            ->method('set');

        $cache->expects($this->at(0))
            ->method('getItem')
            ->with($cacheKey)
            ->willReturn($cacheItem);

        $cache->expects($this->at(1))
            ->method('save')
            ->with($cacheItem);

        $res = $this->adapter->getPath($asset);
        $this->assertSame($expected, $res);
    }
}
