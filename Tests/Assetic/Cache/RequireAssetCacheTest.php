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

use Fxp\Component\RequireAsset\Assetic\Cache\RequireAssetCache;
use Fxp\Component\RequireAsset\Assetic\Cache\RequireAssetCacheInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Require Asset Cache Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequireAssetCacheInterface
     */
    protected $cache;

    protected function setUp()
    {
        $this->cache = new RequireAssetCache($this->getCacheDir(), $this->getCacheName());
    }

    protected function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->getCacheDir());
    }

    protected function getCacheDir()
    {
        return sys_get_temp_dir() . '/fxp_require_asset-cache-test';
    }

    protected function getCacheName()
    {
        return 'require-assets';
    }

    public function testBasic()
    {
        $this->assertFalse($this->cache->hasResources());
        $this->assertSame(array(), $this->cache->getResources());
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
        $resources = array(
            $mb->getMock(),
            $mb->getMock(),
            $mb->getMock(),
        );

        $this->cache->setResources($resources);

        $this->assertTrue($this->cache->hasResources());
        $this->assertSame($resources, $this->cache->getResources());

        $cache = new RequireAssetCache($this->getCacheDir(), $this->getCacheName());

        $this->assertTrue($cache->hasResources());
        $this->assertEquals($resources, $cache->getResources());
    }
}
