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

/**
 * Locale Require Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class LocaleRequireAssetExtensionTest extends AbstractRequireAssetExtensionTest
{
    /**
     * @return array
     */
    public function getLocaleRequireTwigTags()
    {
        return array(
            array('locale_require_script', 'debug'),
            array('locale_require_script', 'prod'),
            array('locale_require_style',  'debug'),
            array('locale_require_style',  'prod'),
        );
    }

    /**
     * @dataProvider getLocaleRequireTwigTags
     * @param string $tag
     * @param string $env
     */
    public function testLocaleRequireAsset($tag, $env)
    {
        $this->localeManager->setLocale('fr_FR');
        $this->factory->setDebug('debug' === $env);

        // require assets
        $this->addAsset('@acme_demo/js/asset.js', '/assets/acemodemo/js/asset.js');
        $this->addAsset('@acme_demo/js/asset2.js', '/assets/acemodemo/js/asset2.js');
        $this->addAsset('@acme_demo/js/asset3.js', '/assets/acemodemo/js/asset3.js');
        $this->addAsset('@acme_demo/css/asset.css', '/assets/acemodemo/css/asset.css');
        $this->addAsset('@acme_demo/css/asset2.css', '/assets/acemodemo/css/asset2.css');
        $this->addAsset('@acme_demo/css/asset3.css', '/assets/acemodemo/css/asset3.css');
        $this->addAsset('@acme_demo/css/asset4.css', '/assets/acemodemo/css/asset4.css');

        // locale require assets
        $this->addAsset('@acme_demo/js/asset2-fr.js', '/assets/acemodemo/js/locale/asset2.fr.js');
        $this->addAsset('@acme_demo/css/asset2-fr.css', '/assets/acemodemo/css/locale/asset2.fr.css');
        $this->addAsset('@acme_demo/js/asset3-fr-fr.js', '/assets/acemodemo/js/locale/asset3.fr-fr.js');
        $this->addAsset('@acme_demo/css/asset3-fr-fr.css', '/assets/acemodemo/css/locale/asset3.fr-fr.css');

        $this->localeManager->addLocaliszedAsset('@acme_demo/js/asset3.js', 'fr_FR', '@acme_demo/js/asset3-fr-fr.js');
        $this->localeManager->addLocaliszedAsset('@acme_demo/css/asset3.css', 'fr_FR', '@acme_demo/css/asset3-fr-fr.css');

        // common require assets
        $this->addFormulaeAsset('common_js', array(
            '@acme_demo/js/asset.js',
            '@acme_demo/js/asset2.js',
        ), '/assets/acemodemo/js/common.js');

        $this->addFormulaeAsset('common_css', array(
            '@acme_demo/css/asset.css',
            '@acme_demo/css/asset2.css',
        ), '/assets/acemodemo/css/common.css');

        // locale common require assets
        $this->addFormulaeAsset('common_js_fr', array(
                '@acme_demo/js/asset2-fr.js',
            ), '/assets/acemodemo/js/common_fr.js');

        $this->addFormulaeAsset('common_css_fr', array(
                '@acme_demo/css/asset2-fr.css',
            ), '/assets/acemodemo/css/common_fr.css');

        $this->localeManager->addLocaliszedAsset('@common_js', 'fr', '@common_js_fr');
        $this->localeManager->addLocaliszedAsset('@common_css', 'fr', '@common_css_fr');

        $this->doValidTagTest($tag, 'test', '_'.$env);
    }
}
