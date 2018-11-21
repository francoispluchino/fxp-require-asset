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

use Fxp\Component\RequireAsset\Webpack\Adapter\MockAdapter;
use PHPUnit\Framework\TestCase;

/**
 * Mock Manifest Adapter Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class MockAdapterTest extends TestCase
{
    /**
     * @var MockAdapter
     */
    protected $adapter;

    protected function setUp()
    {
        $this->adapter = new MockAdapter();
    }

    protected function tearDown()
    {
        $this->adapter = null;
    }

    public function getPathValues()
    {
        return [
            ['@webpack/assets/asset.js', 'js', '/assets/asset.js'],
            ['@webpack/assets/asset.js', 'script', '/assets/asset.js'],
            ['@webpack/assets/asset.css', 'css', '/assets/asset.css'],
            ['@webpack/assets/asset.css', 'style', '/assets/asset.css'],
            ['@webpack/assets/asset_js.js', null, '/assets/asset_js.js'],
            ['@webpack/assets/asset_css.css', null, '/assets/asset_css.css'],
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
     * @expectedExceptionMessage The asset type is required for the asset "assets/asset.ext"
     */
    public function testGetWithInvalidAssetExtension()
    {
        $this->adapter->getPath('assets/asset.ext', null);
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\AssetNotFoundException
     * @expectedExceptionMessage The script asset "assets/asset.ext" is not found
     */
    public function testGetWithInvalidScriptExtension()
    {
        $this->adapter->getPath('assets/asset.ext', 'script');
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\AssetNotFoundException
     * @expectedExceptionMessage The style asset "assets/asset.ext" is not found
     */
    public function testGetWithInvalidStyleExtension()
    {
        $this->adapter->getPath('assets/asset.ext', 'style');
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\InvalidArgumentException
     * @expectedExceptionMessage The asset type is required for the asset "@webpack/asset_not_found"
     */
    public function testGetWithoutAsset()
    {
        $asset = '@webpack/asset_not_found';

        $this->adapter->getPath($asset);
    }
}
