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
 */
class ManifestAdapterTest extends TestCase
{
    /**
     * @var ManifestAdapter
     */
    protected $adapter;

    protected function setUp()
    {
        $this->adapter = new ManifestAdapter(
            realpath(__DIR__.'/../../Fixtures/Webpack/manifest.json')
        );
    }

    protected function tearDown()
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
     * @param string|null $type
     * @param string      $expectedResult
     */
    public function testGet($asset, $type, $expectedResult)
    {
        $res = $this->adapter->getPath($asset, $type);

        $this->assertSame($expectedResult, $res);
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\AssetNotFoundException
     * @expectedExceptionMessage The asset "assets/asset.ext" is not found
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
     * @expectedException \Fxp\Component\RequireAsset\Exception\AssetNotFoundException
     * @expectedExceptionMessage The asset "@webpack/asset_not_found" is not found
     */
    public function testGetWithoutAsset()
    {
        $asset = '@webpack/asset_not_found';

        $this->adapter->getPath($asset);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Asset manifest file "INVALID_MANIFEST.json" does not exist.
     */
    public function testInvalidJsonFilename()
    {
        $this->adapter = new ManifestAdapter('INVALID_MANIFEST.json');

        $this->adapter->getPath('@webpack/asset');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Error parsing JSON from asset manifest file
     */
    public function testInvalidJsonContent()
    {
        $this->adapter = new ManifestAdapter(
            realpath(__DIR__.'/../../Fixtures/Webpack/manifest_invalid.json')
        );

        $this->adapter->getPath('@webpack/asset');
    }
}
