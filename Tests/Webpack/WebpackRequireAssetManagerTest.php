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

use Fxp\Component\RequireAsset\Webpack\WebpackRequireAssetManager;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Webpack Require Asset Manager Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class WebpackRequireAssetManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WebpackRequireAssetManager
     */
    protected $ram;

    protected function setUp()
    {
        $this->ram = new WebpackRequireAssetManager(
            realpath(__DIR__.'/../Fixtures/Webpack/assets.json')
        );
    }

    protected function tearDown()
    {
        $this->ram = null;
    }

    public function testHas()
    {
        $asset = '@webpack/asset_js';
        $expectedResult = true;

        $res = $this->ram->has($asset);

        $this->assertSame($expectedResult, $res);
    }

    public function testHasNotAsset()
    {
        $asset = '@webpack/asset_not_found';
        $expectedResult = false;

        $res = $this->ram->has($asset);

        $this->assertSame($expectedResult, $res);
    }

    public function getPathValues()
    {
        return array(
            array('@webpack/asset', 'js', '/assets/asset.js'),
            array('@webpack/asset', 'script', '/assets/asset.js'),
            array('@webpack/asset', 'css', '/assets/asset.css'),
            array('@webpack/asset', 'style', '/assets/asset.css'),
            array('@webpack/asset_js', null, '/assets/asset_js.js'),
            array('@webpack/asset_css', null, '/assets/asset_css.css'),
            array('@webpack/asset_ext', null, '/assets/asset_ext.ext'),
        );
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
        $res = $this->ram->getPath($asset, $type);

        $this->assertSame($expectedResult, $res);
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\InvalidArgumentException
     * @expectedExceptionMessage The asset type is required for the asset "@webpack/asset_ext2"
     */
    public function testGetWithRequireType()
    {
        $this->ram->getPath('@webpack/asset_ext2');
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\AssetNotFoundException
     * @expectedExceptionMessage The asset "@webpack/asset_not_found" is not found
     */
    public function testGetWithoutAsset()
    {
        $asset = '@webpack/asset_not_found';

        $this->ram->getPath($asset);
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\InvalidArgumentException
     * @expectedExceptionMessage Cannot access "INVALID_ASSET.json" to read the JSON file
     */
    public function testInvalidJsonFilename()
    {
        $this->ram = new WebpackRequireAssetManager('INVALID_ASSET.json');

        $this->ram->getPath('@webpack/asset');
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\InvalidArgumentException
     * @expectedExceptionMessage Cannot read the JSON content: Syntax error
     */
    public function testInvalidJsonContent()
    {
        $this->ram = new WebpackRequireAssetManager(
            realpath(__DIR__.'/../Fixtures/Webpack/assets_invalid.json')
        );

        $this->ram->getPath('@webpack/asset');
    }

    public function testGetPathWithCache()
    {
        /* @var CacheItemPoolInterface|\PHPUnit_Framework_MockObject_MockObject $cache */
        $cache = $this->getMockBuilder(CacheItemPoolInterface::class)->getMock();
        $cacheKey = 'custom_key';
        $asset = '@webpack/asset_js';
        $expected = '/assets/asset_js.js';
        $assetFile = realpath(__DIR__.'/../Fixtures/Webpack/assets.json');

        $this->ram = new WebpackRequireAssetManager(
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

        $res = $this->ram->getPath($asset);
        $this->assertSame($expected, $res);
    }

    public function testGetPathWithEmptyCache()
    {
        /* @var CacheItemPoolInterface|\PHPUnit_Framework_MockObject_MockObject $cache */
        $cache = $this->getMockBuilder(CacheItemPoolInterface::class)->getMock();
        $cacheKey = 'custom_key';
        $asset = '@webpack/asset_js';
        $expected = '/assets/asset_js.js';
        $assetFile = realpath(__DIR__.'/../Fixtures/Webpack/assets.json');

        $this->ram = new WebpackRequireAssetManager(
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

        $res = $this->ram->getPath($asset);
        $this->assertSame($expected, $res);
    }
}
