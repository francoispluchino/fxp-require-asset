<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Assetic\Cache;

use Fxp\Component\RequireAsset\Assetic\Cache\AsseticAssetCache;
use Fxp\Component\RequireAsset\Assetic\Cache\AsseticAssetCacheInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Require Asset Cache Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AsseticAssetCacheTest extends TestCase
{
    /**
     * @var AsseticAssetCacheInterface
     */
    protected $cache;

    protected function setUp()
    {
        $this->cache = new AsseticAssetCache($this->getCacheDir(), $this->getCacheName());
    }

    protected function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->getCacheDir());
    }

    protected function getCacheDir()
    {
        return sys_get_temp_dir().'/fxp_require_asset-cache-test';
    }

    protected function getCacheName()
    {
        return 'require-assets';
    }

    public function testBasic()
    {
        $this->assertFalse($this->cache->hasResources());
        $this->assertSame([], $this->cache->getResources());
        $this->assertTrue($this->cache->hasResources());

        $this->cache->invalidate();

        $this->assertFalse($this->cache->hasResources());
    }

    public function testCacheContent()
    {
        $this->assertFalse($this->cache->hasResources());

        $mb = $this
            ->getMockBuilder('Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResource')
            ->disableOriginalConstructor();
        $resources = [
            $mb->getMock(),
            $mb->getMock(),
            $mb->getMock(),
        ];

        $this->cache->setResources($resources);

        $this->assertTrue($this->cache->hasResources());
        $this->assertSame($resources, $this->cache->getResources());

        $cache = new AsseticAssetCache($this->getCacheDir(), $this->getCacheName());

        $this->assertTrue($cache->hasResources());
        $this->assertEquals($resources, $cache->getResources());
    }
}
