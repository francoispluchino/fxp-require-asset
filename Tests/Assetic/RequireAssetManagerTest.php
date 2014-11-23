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

use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Assetic\FilterManager;
use Fxp\Component\RequireAsset\Assetic\Cache\RequireAssetCache;
use Fxp\Component\RequireAsset\Assetic\Cache\RequireAssetCacheInterface;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManager;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManager;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManager;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManagerInterface;
use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;
use Fxp\Component\RequireAsset\Assetic\RequireAssetManagerInterface;
use Fxp\Component\RequireAsset\Tests\Assetic\Config\PackageTest;

/**
 * Require Asset Manager Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AssetFactory
     */
    protected $factory;

    /**
     * @var FilterManager
     */
    protected $fm;

    /**
     * @var LazyAssetManager
     */
    protected $lam;

    /**
     * @var FileExtensionManagerInterface
     */
    protected $fem;

    /**
     * @var PatternManagerInterface
     */
    protected $ptm;

    /**
     * @var OutputManagerInterface
     */
    protected $om;

    /**
     * @var RequireAssetManagerInterface
     */
    protected $ram;

    protected function setUp()
    {
        $this->factory = new AssetFactory('web');
        $this->fm = new FilterManager();
        $this->factory->setFilterManager($this->fm);
        $this->factory->setDebug(true);

        $this->lam = new LazyAssetManager($this->factory);
        $this->fem = new FileExtensionManager();
        $this->ptm = new PatternManager();
        $this->om = new OutputManager();
        $this->ram = new RequireAssetManager($this->fem, $this->ptm, $this->om);
    }

    protected function tearDown()
    {
        $this->lam = null;
        $this->fem = null;
        $this->ptm = null;
        $this->om = null;
        $this->ram = null;

        PackageTest::cleanFixtures();
    }

    public function testBasicWithoutConstructor()
    {
        $ram = new RequireAssetManager();

        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManagerInterface', $ram->getFileExtensionManager());
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PatternManagerInterface', $ram->getPatternManager());
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\OutputManagerInterface', $ram->getOutputManager());
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PackageManagerInterface', $ram->getPackageManager());
        $this->assertNull($ram->getCache());
    }

    public function testBascic()
    {
        /* @var RequireAssetCacheInterface $cache */
        $cache = $this->getMock('Fxp\Component\RequireAsset\Assetic\Cache\RequireAssetCacheInterface');

        $this->assertSame($this->fem, $this->ram->getFileExtensionManager());
        $this->assertSame($this->ptm, $this->ram->getPatternManager());
        $this->assertSame($this->om, $this->ram->getOutputManager());
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PackageManagerInterface', $this->ram->getPackageManager());
        $this->assertNull($this->ram->getCache());

        $this->ram->setCache($cache);
        $this->assertSame($cache, $this->ram->getCache());
    }

    public function testAddAssetResourcesWithoutAsset()
    {
        $this->assertCount(0, $this->lam->getResources());

        $this->ram->addAssetResources($this->lam);

        $this->assertCount(0, $this->lam->getResources());
    }

    public function testAddResourcesWhithAsset()
    {
        PackageTest::createFixtures();

        $pm = $this->ram->getPackageManager();
        $pm->addPackage('foobar', PackageTest::getFixturesDir().'/foobar', array('js', 'css'), array(), false, false);

        $this->assertCount(0, $this->lam->getResources());

        $this->ram->addAssetResources($this->lam);

        $this->assertCount(9, $this->lam->getResources());
    }

    public function testAddCommonAssets()
    {
        PackageTest::createFixtures();

        $this->assertCount(0, $this->lam->getResources());

        $inputs = array(
            '@foobar/js/component-a.js',
            '@foobar/js/component-b.js',
        );

        $this->ram->addCommonAsset('common_js', $inputs, 'TARGET.js');
        $this->ram->addAssetResources($this->lam);

        $this->assertCount(1, $this->lam->getResources());
    }

    public function testAddResourcesWhithAssetAndCache()
    {
        PackageTest::createFixtures();

        $cache = new RequireAssetCache(PackageTest::getFixturesDir());
        $pm = $this->ram->getPackageManager();
        $pm->addPackage('foobar', PackageTest::getFixturesDir().'/foobar', array('js', 'css'), array(), false, false);

        $this->ram->setCache($cache);

        $this->assertCount(0, $this->lam->getResources());

        $this->ram->addAssetResources($this->lam);
        $resources = $this->lam->getResources();
        $this->assertCount(9, $resources);

        $this->ram->addAssetResources($this->lam);
        $this->assertEquals($resources, $this->lam->getResources());

        $lam = new LazyAssetManager($this->factory);

        $this->assertCount(0, $lam->getResources());
        $this->ram->addAssetResources($lam);
        $this->assertEquals($resources, $this->lam->getResources());
    }
}
