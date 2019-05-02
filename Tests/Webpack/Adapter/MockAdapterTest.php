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
 *
 * @internal
 */
final class MockAdapterTest extends TestCase
{
    /**
     * @var MockAdapter
     */
    protected $adapter;

    protected function setUp(): void
    {
        $this->adapter = new MockAdapter();
    }

    protected function tearDown(): void
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
     * @param null|string $type
     * @param string      $expectedResult
     */
    public function testGet($asset, $type, $expectedResult): void
    {
        $res = $this->adapter->getPath($asset, $type);

        $this->assertSame($expectedResult, $res);
    }

    public function testGetWithInvalidAssetExtension(): void
    {
        $this->expectException(\Fxp\Component\RequireAsset\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('The asset type is required for the asset "assets/asset.ext"');

        $this->adapter->getPath('assets/asset.ext', null);
    }

    public function testGetWithInvalidScriptExtension(): void
    {
        $this->expectException(\Fxp\Component\RequireAsset\Exception\AssetNotFoundException::class);
        $this->expectExceptionMessage('The script asset "assets/asset.ext" is not found');

        $this->adapter->getPath('assets/asset.ext', 'script');
    }

    public function testGetWithInvalidStyleExtension(): void
    {
        $this->expectException(\Fxp\Component\RequireAsset\Exception\AssetNotFoundException::class);
        $this->expectExceptionMessage('The style asset "assets/asset.ext" is not found');

        $this->adapter->getPath('assets/asset.ext', 'style');
    }

    public function testGetWithoutAsset(): void
    {
        $this->expectException(\Fxp\Component\RequireAsset\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('The asset type is required for the asset "@webpack/asset_not_found"');

        $asset = '@webpack/asset_not_found';

        $this->adapter->getPath($asset);
    }
}
