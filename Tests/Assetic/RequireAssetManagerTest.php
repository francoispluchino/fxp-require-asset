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
use Fxp\Component\RequireAsset\Assetic\Config\AssetReplacementManager;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManager;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\LocaleManager;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManager;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PackageManager;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManager;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Factory\Resource\CommonRequireAssetResource;
use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;
use Fxp\Component\RequireAsset\Assetic\RequireAssetManagerInterface;
use Fxp\Component\RequireAsset\Tests\Assetic\Config\PackageTest;
use Symfony\Component\Filesystem\Filesystem;

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
        $this->ram = new RequireAssetManager();
        $this->ram->setFileExtensionManager($this->fem);
        $this->ram->setPatternManager($this->ptm);
        $this->ram->setOutputManager($this->om);
        $this->ram->setLocaleManager(new LocaleManager());
        $this->ram->setPackageManager(new PackageManager($this->fem, $this->ptm));
        $this->ram->setAssetReplacementManager(new AssetReplacementManager());
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

    public function testAddLocalizedCommonAssets()
    {
        PackageTest::createFixtures();
        static::createLocalizedFixtures();

        $this->assertCount(0, $this->lam->getResources());

        $this->ram->getLocaleManager()->addLocalizedAsset('@foobar/js/component-a.js', 'fr', '@foobar/js/component-a-fr.js');
        $this->ram->getLocaleManager()->addLocalizedAsset('@foobar/js/component-a.js', 'fr_FR', '@foobar/js/component-a-fr-fr.js');
        $this->ram->getLocaleManager()->addLocalizedAsset('@foobar/js/component-a.js', 'en_US', '@foobar/js/component-a-en-us.js');
        $this->ram->getLocaleManager()->addLocalizedAsset('@foobar/js/component-b.js', 'fr', '@foobar/js/component-b-fr.js');
        $this->ram->getLocaleManager()->addLocalizedAsset('@foobar/js/component-b.js', 'en_US', '@foobar/js/component-b-en-us.js');

        $inputs = array(
            '@foobar/js/component-a.js',
            '@foobar/js/component-b.js',
            '@foobar/js/component-c.js',
        );

        $this->ram->addCommonAsset('common_js', $inputs, 'TARGET.js');
        $this->ram->addCommonAsset('common_js__fr_fr', array('@foobar_js_component_a_fr_fr_js', '@foobar_js_component_b_fr_js'), 'TARGET-fr-fr-custom.js');
        $this->ram->addAssetResources($this->lam);

        $validLocales = array(
            'fr',
            'fr_fr',
            'en_us',
        );
        $this->assertSame($validLocales, $this->ram->getLocaleManager()->getAssetLocales());

        $validResources = array(
            new CommonRequireAssetResource('common_js', $inputs, 'assets/TARGET.js', array(), array()),
            new CommonRequireAssetResource('common_js__fr', array('@foobar_js_component_a_fr_js', '@foobar_js_component_b_fr_js'), 'assets/TARGET-fr.js', array(), array()),
            new CommonRequireAssetResource('common_js__en_us', array('@foobar_js_component_a_en_us_js', '@foobar_js_component_b_en_us_js'), 'assets/TARGET-en-us.js', array(), array()),
            new CommonRequireAssetResource('common_js__fr_fr', array('@foobar_js_component_a_fr_fr_js', '@foobar_js_component_b_fr_js'), 'assets/TARGET-fr-fr-custom.js', array(), array()),
        );
        $this->assertEquals($validResources, $this->lam->getResources());
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

    public function testReplaceAsset()
    {
        PackageTest::createFixtures();

        $pm = $this->ram->getPackageManager();
        $pm->addPackage('foobar', PackageTest::getFixturesDir().'/foobar', array('js', 'less'), array(), false, false);

        $arm = $this->ram->getAssetReplacementManager();
        $arm->addReplacement('@foobar/less/foobar.less', '@foobar/less/foobar-theme.less');

        $this->assertCount(0, $this->lam->getResources());

        $this->ram->addAssetResources($this->lam);

        $this->assertCount(10, $this->lam->getResources());
    }

    public function testNonexistentAssetReplacement()
    {
        $arm = $this->ram->getAssetReplacementManager();
        $arm->addReplacement('@foobar/less/foobar.less', '@foobar/less/foobar-theme.less');

        $this->assertCount(0, $this->lam->getResources());

        $this->ram->addAssetResources($this->lam);

        $this->assertCount(0, $this->lam->getResources());
    }

    public static function createLocalizedFixtures()
    {
        $fs = new Filesystem();

        foreach (static::getLocalizedFixtureFiles() as $filename) {
            $fs->dumpFile(PackageTest::getFixturesDir().'/'.$filename, '');
        }
    }

    /**
     * @return array
     */
    public static function getLocalizedFixtureFiles()
    {
        return array(
            'foobar/js/component-a-fr.js',
            'foobar/js/component-a-fr-fr.js',
            'foobar/js/component-a-en-us.js',
            'foobar/js/component-b-fr.js',
            'foobar/js/component-b-en-us.js',
        );
    }
}
