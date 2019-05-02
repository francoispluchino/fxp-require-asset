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

use Fxp\Component\RequireAsset\Webpack\Adapter\ManifestAdapter;
use PHPUnit\Framework\TestCase;

/**
 * Webpack Manifest Adapter Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 *
 * @internal
 */
final class ManifestAdapterTest extends TestCase
{
    /**
     * @var ManifestAdapter
     */
    protected $adapter;

    protected function setUp(): void
    {
        $this->adapter = new ManifestAdapter(
            realpath(__DIR__.'/../../Fixtures/Webpack/manifest.json')
        );
    }

    protected function tearDown(): void
    {
        $this->adapter = null;
    }

    public function getPathValues()
    {
        return [
            ['@webpack/assets/asset.js', 'js', '/assets/asset.110f5173.js'],
            ['@webpack/assets/asset.js', 'script', '/assets/asset.110f5173.js'],
            ['@webpack/assets/asset.css', 'css', '/assets/asset.570eb838.css'],
            ['@webpack/assets/asset.css', 'style', '/assets/asset.570eb838.css'],
            ['@webpack/assets/asset_js.js', null, '/assets/asset_js.a1adea65.js'],
            ['@webpack/assets/asset_css.css', null, '/assets/asset_css.e18bbf61.css'],
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
        $this->expectException(\Fxp\Component\RequireAsset\Exception\AssetNotFoundException::class);
        $this->expectExceptionMessage('The asset "assets/asset.ext" is not found');

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
        $this->expectException(\Fxp\Component\RequireAsset\Exception\AssetNotFoundException::class);
        $this->expectExceptionMessage('The asset "@webpack/asset_not_found" is not found');

        $asset = '@webpack/asset_not_found';

        $this->adapter->getPath($asset);
    }

    public function testInvalidJsonFilename(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Asset manifest file "INVALID_MANIFEST.json" does not exist.');

        $this->adapter = new ManifestAdapter('INVALID_MANIFEST.json');

        $this->adapter->getPath('@webpack/asset');
    }

    public function testInvalidJsonContent(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Error parsing JSON from asset manifest file');

        $this->adapter = new ManifestAdapter(
            realpath(__DIR__.'/../../Fixtures/Webpack/manifest_invalid.json')
        );

        $this->adapter->getPath('@webpack/asset');
    }
}
