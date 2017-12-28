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
        $this->debugCommonAssets = [
            'common_js' => [
                '@acme_demo/js/asset.js',
                '@acme_demo/js/asset2.js',
            ],
            'common_css' => [
                '@acme_demo/css/asset.css',
                '@acme_demo/css/asset2.css',
            ],
        ];

        parent::setUp();
    }

    /**
     * @return array
     */
    public function getDebugRequireTwigTags()
    {
        return [
            ['debug_require_script'],
            ['debug_require_style'],
        ];
    }

    /**
     * @dataProvider getDebugRequireTwigTags
     *
     * @param string $tag
     */
    public function testDebugRequireAsset($tag)
    {
        $this->factory->setDebug(true);
        $this->addRequireAssets();
        $this->doValidTagTest($tag, 'test', '_debug');
    }

    /**
     * @dataProvider getDebugRequireTwigTags
     *
     * @param string $tag
     *
     * @expectedException \Twig_Error_Runtime
     */
    public function testDebugRequireAssetInProd($tag)
    {
        $this->factory->setDebug(false);
        $this->addRequireAssets();
        $this->doValidTagTest($tag, 'test', '_prod');
    }

    /**
     * Add the require assets.
     */
    private function addRequireAssets()
    {
        $this->addAsset('@acme_demo/js/asset.js', '/assets/acemodemo/js/asset.js');
        $this->addAsset('@acme_demo/js/asset2.js', '/assets/acemodemo/js/asset2.js');
        $this->addAsset('@acme_demo/js/asset3.js', '/assets/acemodemo/js/asset3.js');
        $this->addAsset('@acme_demo/css/asset.css', '/assets/acemodemo/css/asset.css');
        $this->addAsset('@acme_demo/css/asset2.css', '/assets/acemodemo/css/asset2.css');
        $this->addAsset('@acme_demo/css/asset3.css', '/assets/acemodemo/css/asset3.css');
        $this->addAsset('@acme_demo/css/asset4.css', '/assets/acemodemo/css/asset4.css');
    }
}
