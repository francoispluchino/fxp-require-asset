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
 * Debug Require Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class DebugRequireAssetExtensionTest extends AbstractRequireAssetExtensionTest
{
    protected function setUp()
    {
        $this->debugCommonAssets = array(
            'common_js' => array(
                '@acme_demo/js/asset.js',
                '@acme_demo/js/asset2.js',
            ),
            'common_css' => array(
                '@acme_demo/css/asset.css',
                '@acme_demo/css/asset2.css',
            ),
        );

        parent::setUp();
    }

    /**
     * @return array
     */
    public function getDebugRequireTwigTags()
    {
        return array(
            array('debug_require_script', 'debug'),
            array('debug_require_script', 'prod'),
            array('debug_require_style',  'debug'),
            array('debug_require_style',  'prod'),
        );
    }

    /**
     * @dataProvider getDebugRequireTwigTags
     *
     * @param string $tag
     * @param string $env
     */
    public function testDebugRequireAsset($tag, $env)
    {
        $this->factory->setDebug('debug' === $env);

        // require assets
        $this->addAsset('@acme_demo/js/asset.js', '/assets/acemodemo/js/asset.js');
        $this->addAsset('@acme_demo/js/asset2.js', '/assets/acemodemo/js/asset2.js');
        $this->addAsset('@acme_demo/js/asset3.js', '/assets/acemodemo/js/asset3.js');
        $this->addAsset('@acme_demo/css/asset.css', '/assets/acemodemo/css/asset.css');
        $this->addAsset('@acme_demo/css/asset2.css', '/assets/acemodemo/css/asset2.css');
        $this->addAsset('@acme_demo/css/asset3.css', '/assets/acemodemo/css/asset3.css');
        $this->addAsset('@acme_demo/css/asset4.css', '/assets/acemodemo/css/asset4.css');

        if ('debug' !== $env) {
            // exception because the common asset is not added as formulae asset
            $this->setExpectedException('\Twig_Error_Runtime');
        }

        $this->doValidTagTest($tag, 'test', '_'.$env);
    }
}
