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
use Fxp\Component\RequireAsset\Asset\Config\AssetReplacementManager;
use Fxp\Component\RequireAsset\Asset\Config\LocaleManager;
use Fxp\Component\RequireAsset\Assetic\AsseticAssetManager;
use Fxp\Component\RequireAsset\Assetic\AsseticAssetManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Cache\AsseticAssetCache;
use Fxp\Component\RequireAsset\Assetic\Cache\AsseticAssetCacheInterface;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManager;
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManager;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PackageManager;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManager;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Factory\Resource\CommonRequireAssetResource;
use Fxp\Component\RequireAsset\Tests\Assetic\Config\PackageTest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Assetic Asset Manager Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AsseticAssetManagerTest extends TestCase
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
     * @var AsseticAssetManagerInterface
     */
    protected $aam;

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
        $this->aam = new AsseticAssetManager();
        $this->aam->setFileExtensionManager($this->fem);
        $this->aam->setPatternManager($this->ptm);
        $this->aam->setOutputManager($this->om);
        $this->aam->setLocaleManager(new LocaleManager());
        $this->aam->setPackageManager(new PackageManager($this->fem, $this->ptm));
        $this->aam->setAssetReplacementManager(new AssetReplacementManager());
    }

    protected function tearDown()
    {
        $this->lam = null;
        $this->fem = null;
        $this->ptm = null;
        $this->om = null;
        $this->aam = null;

        PackageTest::cleanFixtures();
    }

    public function testBasicWithoutConstructor()
    {
        $aam = new AsseticAssetManager();

        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManagerInterface', $aam->getFileExtensionManager());
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PatternManagerInterface', $aam->getPatternManager());
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\OutputManagerInterface', $aam->getOutputManager());
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PackageManagerInterface', $aam->getPackageManager());
        $this->assertNull($aam->getCache());
    }

    public function testBasic()
    {
        /* @var AsseticAssetCacheInterface $cache */
        $cache = $this->getMockBuilder('Fxp\Component\RequireAsset\Assetic\Cache\AsseticAssetCacheInterface')->getMock();

        $this->assertSame($this->fem, $this->aam->getFileExtensionManager());
        $this->assertSame($this->ptm, $this->aam->getPatternManager());
        $this->assertSame($this->om, $this->aam->getOutputManager());
        $this->assertInstanceOf('Fxp\Component\RequireAsset\Assetic\Config\PackageManagerInterface', $this->aam->getPackageManager());
        $this->assertNull($this->aam->getCache());

        $this->aam->setCache($cache);
        $this->assertSame($cache, $this->aam->getCache());
    }

    public function testAddAssetResourcesWithoutAsset()
    {
        $this->assertCount(0, $this->lam->getResources());

        $this->aam->addAssetResources($this->lam);

        $this->assertCount(0, $this->lam->getResources());
    }

    public function testAddResourcesWhithAsset()
    {
        PackageTest::createFixtures();

        $pm = $this->aam->getPackageManager();
        $pm->addPackage('foobar', PackageTest::getFixturesDir().'/foobar', ['js', 'css'], [], false, false);

        $this->assertCount(0, $this->lam->getResources());

        $this->aam->addAssetResources($this->lam);

        $this->assertCount(9, $this->lam->getResources());
    }

    public function testAddCommonAssets()
    {
        PackageTest::createFixtures();

        $this->assertCount(0, $this->lam->getResources());

        $inputs = [
            '@foobar/js/component-a.js',
            '@foobar/js/component-b.js',
        ];

        $this->aam->addCommonAsset('common_js', $inputs, 'TARGET.js');
        $this->aam->addAssetResources($this->lam);

        $this->assertCount(1, $this->lam->getResources());
    }

    public function testAddLocalizedCommonAssets()
    {
        PackageTest::createFixtures();
        static::createLocalizedFixtures();

        $this->assertCount(0, $this->lam->getResources());

        $this->aam->getLocaleManager()->addLocalizedAsset('@foobar/js/component-a.js', 'fr', '@foobar/js/component-a-fr.js');
        $this->aam->getLocaleManager()->addLocalizedAsset('@foobar/js/component-a.js', 'fr_FR', '@foobar/js/component-a-fr-fr.js');
        $this->aam->getLocaleManager()->addLocalizedAsset('@foobar/js/component-a.js', 'en_US', '@foobar/js/component-a-en-us.js');
        $this->aam->getLocaleManager()->addLocalizedAsset('@foobar/js/component-b.js', 'fr', '@foobar/js/component-b-fr.js');
        $this->aam->getLocaleManager()->addLocalizedAsset('@foobar/js/component-b.js', 'en_US', '@foobar/js/component-b-en-us.js');

        $inputs = [
            '@foobar/js/component-a.js',
            '@foobar/js/component-b.js',
            '@foobar/js/component-c.js',
        ];

        $this->aam->addCommonAsset('common_js', $inputs, 'TARGET.js');
        $this->aam->addCommonAsset('common_js__fr_fr', ['@foobar_js_component_a_fr_fr_js', '@foobar_js_component_b_fr_js'], 'TARGET-fr-fr-custom.js');
        $this->aam->addAssetResources($this->lam);

        $validLocales = [
            'fr',
            'fr_fr',
            'en_us',
        ];
        $this->assertSame($validLocales, $this->aam->getLocaleManager()->getAssetLocales());

        $validResources = [
            new CommonRequireAssetResource('common_js', $inputs, 'assets/TARGET.js', [], []),
            new CommonRequireAssetResource('common_js__fr', ['@foobar_js_component_a_fr_js', '@foobar_js_component_b_fr_js'], 'assets/TARGET-fr.js', [], []),
            new CommonRequireAssetResource('common_js__en_us', ['@foobar_js_component_a_en_us_js', '@foobar_js_component_b_en_us_js'], 'assets/TARGET-en-us.js', [], []),
            new CommonRequireAssetResource('common_js__fr_fr', ['@foobar_js_component_a_fr_fr_js', '@foobar_js_component_b_fr_js'], 'assets/TARGET-fr-fr-custom.js', [], []),
        ];
        $this->assertEquals($validResources, $this->lam->getResources());
    }

    public function testAddResourcesWhithAssetAndCache()
    {
        PackageTest::createFixtures();

        $cache = new AsseticAssetCache(PackageTest::getFixturesDir());
        $pm = $this->aam->getPackageManager();
        $pm->addPackage('foobar', PackageTest::getFixturesDir().'/foobar', ['js', 'css'], [], false, false);

        $this->aam->setCache($cache);

        $this->assertCount(0, $this->lam->getResources());

        $this->aam->addAssetResources($this->lam);
        $resources = $this->lam->getResources();
        $this->assertCount(9, $resources);

        $this->aam->addAssetResources($this->lam);
        $this->assertEquals($resources, $this->lam->getResources());

        $lam = new LazyAssetManager($this->factory);

        $this->assertCount(0, $lam->getResources());
        $this->aam->addAssetResources($lam);
        $this->assertEquals($resources, $this->lam->getResources());
    }

    public function testReplaceAsset()
    {
        PackageTest::createFixtures();

        $pm = $this->aam->getPackageManager();
        $pm->addPackage('foobar', PackageTest::getFixturesDir().'/foobar', ['js', 'less'], [], false, false);

        $arm = $this->aam->getAssetReplacementManager();
        $arm->addReplacement('@foobar/less/foobar.less', '@foobar/less/foobar-theme.less');

        $this->assertCount(0, $this->lam->getResources());

        $this->aam->addAssetResources($this->lam);

        $this->assertCount(10, $this->lam->getResources());
    }

    public function testNonexistentAssetReplacement()
    {
        $arm = $this->aam->getAssetReplacementManager();
        $arm->addReplacement('@foobar/less/foobar.less', '@foobar/less/foobar-theme.less');

        $this->assertCount(0, $this->lam->getResources());

        $this->aam->addAssetResources($this->lam);

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
        return [
            'foobar/js/component-a-fr.js',
            'foobar/js/component-a-fr-fr.js',
            'foobar/js/component-a-en-us.js',
            'foobar/js/component-b-fr.js',
            'foobar/js/component-b-en-us.js',
        ];
    }
}
