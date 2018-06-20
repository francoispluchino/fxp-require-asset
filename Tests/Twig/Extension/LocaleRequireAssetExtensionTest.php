<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Twig\Extension;

use Fxp\Component\RequireAsset\Asset\Config\LocaleManager;

/**
 * Locale Require Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class LocaleRequireAssetExtensionTest extends AbstractAssetExtensionTest
{
    protected function setUp()
    {
        $this->localeManager = new LocaleManager();

        parent::setUp();
    }

    /**
     * @return array
     */
    public function getLocaleRequireTwigTags()
    {
        return [
            ['locale_require_script'],
            ['locale_require_style'],
        ];
    }

    /**
     * @dataProvider getLocaleRequireTwigTags
     *
     * @param string $tag
     */
    public function testLocaleRequireAsset($tag)
    {
        $this->localeManager->setLocale('fr_FR');

        $this->localeManager->addLocalizedAsset('@webpack/asset_js', 'fr_FR', '@webpack/asset_js_fr');
        $this->localeManager->addLocalizedAsset('@webpack/asset_css', 'fr_FR', '@webpack/asset_css_fr');

        $this->doValidTagTest($tag);
    }
}
