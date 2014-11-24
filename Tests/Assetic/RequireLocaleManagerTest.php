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

use Fxp\Component\RequireAsset\Assetic\RequireLocaleManager;

/**
 * Require Locale Manager Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireLocaleManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicWithoutAsset()
    {
        $rlm = new RequireLocaleManager();

        $this->assertSame(strtolower(\Locale::getDefault()), $rlm->getLocale());
        $this->assertNull($rlm->getFallbackLocale());
        $this->assertFalse($rlm->hasLocalizedAsset('@asset/source/path.ext'));
        $this->assertCount(0, $rlm->getLocalizedAsset('@asset/source/path.ext'));
        $this->assertFalse($rlm->hasAssetLocale(\Locale::getDefault()));
        $this->assertCount(0, $rlm->getAssetLocales());

        // cache
        $this->assertCount(0, $rlm->getLocalizedAsset('@asset/source/path.ext'));
    }

    public function testWithDefaultLocaleAsset()
    {
        $valid = array(
            '@asset/source/path/locale/'.\Locale::getDefault().'.ext',
        );

        $rlm = new RequireLocaleManager();
        $rlm->addLocaliszedAsset('@asset/source/path.ext', \Locale::getDefault(), $valid[0]);

        $this->assertTrue($rlm->hasLocalizedAsset('@asset/source/path.ext'));
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
        $this->assertTrue($rlm->hasAssetLocale(\Locale::getDefault()));
        $this->assertSame(array(strtolower(\Locale::getDefault())), $rlm->getAssetLocales());

        // cache
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
    }

    public function testWithDefaultLocaleArrayAsset()
    {
        $valid = array(
            '@asset/source/path/locale/'.\Locale::getDefault().'.ext',
        );

        $rlm = new RequireLocaleManager();
        $rlm->addLocaliszedAsset('@asset/source/path.ext', \Locale::getDefault(), $valid);

        $this->assertTrue($rlm->hasLocalizedAsset('@asset/source/path.ext'));
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
        $this->assertTrue($rlm->hasAssetLocale(\Locale::getDefault()));
        $this->assertSame(array(strtolower(\Locale::getDefault())), $rlm->getAssetLocales());

        // cache
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
    }

    public function testParentLocale()
    {
        $valid = array(
            '@asset/source/path/locale/fr.ext',
        );

        $rlm = new RequireLocaleManager();
        $rlm->setLocale('fr_FR');
        $rlm->addLocaliszedAsset('@asset/source/path.ext', 'fr', $valid);

        $this->assertFalse($rlm->hasLocalizedAsset('@asset/source/path.ext'));
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
        $this->assertFalse($rlm->hasAssetLocale('fr_FR'));
        $this->assertTrue($rlm->hasAssetLocale('fr'));
        $this->assertSame(array(strtolower('fr')), $rlm->getAssetLocales());

        // cache
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
    }

    public function testFallbackLocale()
    {
        $valid = array(
            '@asset/source/path/locale/en.ext',
        );

        $rlm = new RequireLocaleManager();
        $rlm->setLocale('fr_FR');
        $rlm->setFallbackLocale('en_US');
        $rlm->addLocaliszedAsset('@asset/source/path.ext', 'en', $valid);

        $this->assertFalse($rlm->hasLocalizedAsset('@asset/source/path.ext'));
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
        $this->assertFalse($rlm->hasAssetLocale('fr_FR'));
        $this->assertFalse($rlm->hasAssetLocale('en_US'));
        $this->assertTrue($rlm->hasAssetLocale('en'));
        $this->assertSame(array(strtolower('en')), $rlm->getAssetLocales());

        // cache
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
    }

    public function testEmptyFallbackLocale()
    {
        $valid = array(
            '@asset/source/path/locale/en.ext',
        );

        $rlm = new RequireLocaleManager();
        $rlm->setLocale('fr_FR');
        $rlm->addLocaliszedAsset('@asset/source/path.ext', 'en', $valid);

        $this->assertFalse($rlm->hasLocalizedAsset('@asset/source/path.ext'));
        $this->assertCount(0, $rlm->getLocalizedAsset('@asset/source/path.ext'));
        $this->assertFalse($rlm->hasAssetLocale('fr_FR'));
        $this->assertTrue($rlm->hasAssetLocale('en'));
        $this->assertSame(array(strtolower('en')), $rlm->getAssetLocales());

        // cache
        $this->assertCount(0, $rlm->getLocalizedAsset('@asset/source/path.ext'));
    }

    public function testRemoveLocalizedAsset()
    {
        $rlm = new RequireLocaleManager();

        $rlm->addLocaliszedAsset('@asset/source/path.ext', 'en', '@asset/source/path/locale/en.ext');
        $this->assertTrue($rlm->hasAssetLocale('en'));
        $this->assertCount(1, $rlm->getAssetLocales());

        $rlm->addLocaliszedAsset('@asset/source/path2.ext', 'en', '@asset/source/path2/locale/en.ext');
        $this->assertTrue($rlm->hasAssetLocale('en'));
        $this->assertCount(1, $rlm->getAssetLocales());

        $rlm->removeLocaliszedAsset('@asset/source/path2.ext', 'en');
        $this->assertCount(1, $rlm->getAssetLocales());

        $rlm->removeLocaliszedAsset('@asset/source/path.ext', 'en');
        $this->assertCount(0, $rlm->getAssetLocales());
    }
}
