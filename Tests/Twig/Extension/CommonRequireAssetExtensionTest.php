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
 * Common Require Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class CommonRequireAssetExtensionTest extends AbstractRequireAssetExtensionTest
{
    /**
     * @return array
     */
    public function getCommonRequireTwigTags()
    {
        return [
            ['common_require_script', 'debug'],
            ['common_require_script', 'prod'],
            ['common_require_style',  'debug'],
            ['common_require_style',  'prod'],
        ];
    }

    /**
     * @dataProvider getCommonRequireTwigTags
     *
     * @param string $tag
     * @param string $env
     */
    public function testCommonRequireAsset($tag, $env)
    {
        $this->factory->setDebug('debug' === $env);

        $this->addAsset('@acme_demo/js/asset.js', '/assets/acemodemo/js/asset.js');
        $this->addAsset('@acme_demo/js/asset2.js', '/assets/acemodemo/js/asset2.js');
        $this->addAsset('@acme_demo/js/asset3.js', '/assets/acemodemo/js/asset3.js');
        $this->addAsset('@acme_demo/css/asset.css', '/assets/acemodemo/css/asset.css');
        $this->addAsset('@acme_demo/css/asset2.css', '/assets/acemodemo/css/asset2.css');
        $this->addAsset('@acme_demo/css/asset3.css', '/assets/acemodemo/css/asset3.css');
        $this->addAsset('@acme_demo/css/asset4.css', '/assets/acemodemo/css/asset4.css');

        $this->addFormulaeAsset('common_js', [
            '@acme_demo/js/asset.js',
            '@acme_demo/js/asset2.js',
        ], '/assets/acemodemo/js/common.js');

        $this->addFormulaeAsset('common_css', [
            '@acme_demo/css/asset.css',
            '@acme_demo/css/asset2.css',
        ], '/assets/acemodemo/css/common.css');

        $this->addFormulaeAsset('not_common_css', [
            '@acme_demo/css/asset4.css',
        ], '/assets/acemodemo/css/not_common.css', false);

        $this->doValidTagTest($tag, 'test', '_'.$env);
    }
}
