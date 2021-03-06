<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Asset\Config;

use Fxp\Component\RequireAsset\Asset\Config\LocaleManager;
use PHPUnit\Framework\TestCase;

/**
 * Require Locale Manager Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 *
 * @internal
 */
final class LocaleManagerTest extends TestCase
{
    public function testBasicWithoutAsset(): void
    {
        $rlm = new LocaleManager();

        $this->assertSame(strtolower(\Locale::getDefault()), $rlm->getLocale());
        $this->assertNull($rlm->getFallbackLocale());
        $this->assertFalse($rlm->hasLocalizedAsset('@asset/source/path.ext'));
        $this->assertCount(0, $rlm->getLocalizedAsset('@asset/source/path.ext'));
        $this->assertFalse($rlm->hasAssetLocale(\Locale::getDefault()));
        $this->assertCount(0, $rlm->getAssetLocales());
        $this->assertCount(0, $rlm->getLocalizedAssets());

        // cache
        $this->assertCount(0, $rlm->getLocalizedAsset('@asset/source/path.ext'));
    }

    public function testWithDefaultLocaleAsset(): void
    {
        $valid = [
            '@asset/source/path/locale/'.\Locale::getDefault().'.ext',
        ];

        $rlm = new LocaleManager();
        $rlm->addLocalizedAsset('@asset/source/path.ext', \Locale::getDefault(), $valid[0]);

        $this->assertTrue($rlm->hasLocalizedAsset('@asset/source/path.ext'));
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
        $this->assertTrue($rlm->hasAssetLocale(\Locale::getDefault()));
        $this->assertSame([strtolower(\Locale::getDefault())], $rlm->getAssetLocales());
        $this->assertSame([strtolower(\Locale::getDefault())], $rlm->getAssetLocales('@asset/source/path.ext'));

        // cache
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
    }

    public function testWithDefaultLocaleArrayAsset(): void
    {
        $valid = [
            '@asset/source/path/locale/'.\Locale::getDefault().'.ext',
        ];

        $rlm = new LocaleManager();
        $rlm->addLocalizedAsset('@asset/source/path.ext', \Locale::getDefault(), $valid);

        $this->assertTrue($rlm->hasLocalizedAsset('@asset/source/path.ext'));
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
        $this->assertTrue($rlm->hasAssetLocale(\Locale::getDefault()));
        $this->assertSame([strtolower(\Locale::getDefault())], $rlm->getAssetLocales());
        $this->assertSame([strtolower(\Locale::getDefault())], $rlm->getAssetLocales('@asset/source/path.ext'));

        // cache
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
    }

    public function testParentLocale(): void
    {
        $valid = [
            '@asset/source/path/locale/fr.ext',
        ];

        $rlm = new LocaleManager();
        $rlm->setLocale('fr_FR');
        $rlm->addLocalizedAsset('@asset/source/path.ext', 'fr', $valid);

        $this->assertFalse($rlm->hasLocalizedAsset('@asset/source/path.ext'));
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
        $this->assertFalse($rlm->hasAssetLocale('fr_FR'));
        $this->assertTrue($rlm->hasAssetLocale('fr'));
        $this->assertSame([strtolower('fr')], $rlm->getAssetLocales());
        $this->assertSame([strtolower('fr')], $rlm->getAssetLocales('@asset/source/path.ext'));

        // cache
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
    }

    public function testFallbackLocale(): void
    {
        $valid = [
            '@asset/source/path/locale/en.ext',
        ];

        $rlm = new LocaleManager();
        $rlm->setLocale('fr_FR');
        $rlm->setFallbackLocale('en_US');
        $rlm->addLocalizedAsset('@asset/source/path.ext', 'en', $valid);

        $this->assertFalse($rlm->hasLocalizedAsset('@asset/source/path.ext'));
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
        $this->assertFalse($rlm->hasAssetLocale('fr_FR'));
        $this->assertFalse($rlm->hasAssetLocale('en_US'));
        $this->assertTrue($rlm->hasAssetLocale('en'));
        $this->assertSame([strtolower('en')], $rlm->getAssetLocales());
        $this->assertSame([strtolower('en')], $rlm->getAssetLocales('@asset/source/path.ext'));

        // cache
        $this->assertSame($valid, $rlm->getLocalizedAsset('@asset/source/path.ext'));
    }

    public function testEmptyFallbackLocale(): void
    {
        $valid = [
            '@asset/source/path/locale/en.ext',
        ];

        $rlm = new LocaleManager();
        $rlm->setLocale('fr_FR');
        $rlm->addLocalizedAsset('@asset/source/path.ext', 'en', $valid);

        $this->assertFalse($rlm->hasLocalizedAsset('@asset/source/path.ext'));
        $this->assertCount(0, $rlm->getLocalizedAsset('@asset/source/path.ext'));
        $this->assertFalse($rlm->hasAssetLocale('fr_FR'));
        $this->assertTrue($rlm->hasAssetLocale('en'));
        $this->assertSame([strtolower('en')], $rlm->getAssetLocales());
        $this->assertSame([strtolower('en')], $rlm->getAssetLocales('@asset/source/path.ext'));

        // cache
        $this->assertCount(0, $rlm->getLocalizedAsset('@asset/source/path.ext'));
    }

    public function testRemoveLocalizedAsset(): void
    {
        $rlm = new LocaleManager();

        $rlm->addLocalizedAsset('@asset/source/path.ext', 'en', '@asset/source/path/locale/en.ext');
        $this->assertTrue($rlm->hasAssetLocale('en'));
        $this->assertCount(1, $rlm->getAssetLocales());
        $this->assertSame([strtolower('en')], $rlm->getAssetLocales('@asset/source/path.ext'));

        $rlm->addLocalizedAsset('@asset/source/path2.ext', 'en', '@asset/source/path2/locale/en.ext');
        $this->assertTrue($rlm->hasAssetLocale('en'));
        $this->assertCount(1, $rlm->getAssetLocales());
        $this->assertSame([strtolower('en')], $rlm->getAssetLocales('@asset/source/path2.ext'));

        $rlm->removeLocalizedAsset('@asset/source/path2.ext', 'en');
        $this->assertCount(1, $rlm->getAssetLocales());
        $this->assertSame([strtolower('en')], $rlm->getAssetLocales('@asset/source/path.ext'));
        $this->assertSame([], $rlm->getAssetLocales('@asset/source/path2.ext'));

        $rlm->removeLocalizedAsset('@asset/source/path.ext', 'en');
        $this->assertCount(0, $rlm->getAssetLocales());
        $this->assertSame([], $rlm->getAssetLocales('@asset/source/path.ext'));
    }
}
