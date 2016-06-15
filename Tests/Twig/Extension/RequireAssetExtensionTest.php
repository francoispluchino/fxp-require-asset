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

use Fxp\Component\RequireAsset\Tag\RequireScriptTag;
use Fxp\Component\RequireAsset\Tag\RequireStyleTag;

/**
 * Require Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetExtensionTest extends AbstractRequireAssetExtensionTest
{
    /**
     * @return array
     */
    public function getContainerServiceConfig()
    {
        $configs = array();

        foreach ($this->getRequireTwigTags() as $tags) {
            $configs[] = array($tags[0], false, false, false, 'The twig tag "%s" require the container service');
            $configs[] = array($tags[0], true,  false, false, 'The twig tag "%s" require the service "assetic.asset_manager"');
            $configs[] = array($tags[0], true,  true,  false, 'The twig tag "%s" require the service "templating.helper.assets"');
        }

        return $configs;
    }

    /**
     * @dataProvider getRequireTwigTags
     *
     * @param string $tag
     *
     * @expectedException \Fxp\Component\RequireAsset\Exception\Twig\RequireTagException
     * @expectedExceptionMessage is not managed by the Assetic Manager
     */
    public function testAssetIsNotManagedByAsseticManager($tag)
    {
        $this->doValidTagTest($tag);
    }

    /**
     * @dataProvider getRequireTwigTags
     *
     * @param string $tag
     */
    public function testTwigTags($tag)
    {
        $this->addAsset('@acme_demo/js/asset.js', '/assets/acemodemo/js/asset.js');
        $this->addAsset('@acme_demo/css/asset.css', '/assets/acemodemo/css/asset.css');

        $this->doValidTagTest($tag);
    }

    /**
     * @dataProvider getRequireTwigTags
     *
     * @param string $tag
     */
    public function testTwigTagsWithMultiAsset($tag)
    {
        $this->addAsset('@acme_demo/js/asset.js', '/assets/acemodemo/js/asset.js');
        $this->addAsset('@acme_demo/js/asset2.js', '/assets/acemodemo/js/asset2.js');
        $this->addAsset('@acme_demo/css/asset.css', '/assets/acemodemo/css/asset.css');
        $this->addAsset('@acme_demo/css/asset2.css', '/assets/acemodemo/css/asset2.css');

        $this->doValidTagTest($tag, 'test_multi_asset');
    }

    /**
     * @dataProvider getRequireTwigTags
     *
     * @param string $tag
     *
     * @expectedException \Twig_Error_Syntax
     * @expectedExceptionMessageRegExp /The twig tag "(\w+)" require a lest one asset/
     */
    public function testTwigTagsWithoutAssetInTag($tag)
    {
        $this->doValidTagTest($tag, 'test_without_asset');
    }

    /**
     * @dataProvider getRequireTwigTags
     *
     * @param string $tag
     */
    public function testReplacementAsset($tag)
    {
        $this->addAsset('@acme_demo/js/asset.js', '/assets/acemodemo/js/asset.js');
        $this->addAsset('@acme_demo/css/asset.css', '/assets/acemodemo/css/asset.css');
        $this->replacementManager->addReplacement('virtual_asset/js/asset.js', '@acme_demo/js/asset.js');
        $this->replacementManager->addReplacement('virtual_asset/css/asset.css', '@acme_demo/css/asset.css');

        $this->doValidTagTest($tag, 'test_replacement_asset');
    }

    /**
     * @dataProvider getRequireTwigTags
     *
     * @param string $tag
     */
    public function testOptionalAsset($tag)
    {
        $this->addAsset('@acme_demo/js/asset.js', '/assets/acemodemo/js/asset.js');
        $this->addAsset('@acme_demo/css/asset.css', '/assets/acemodemo/css/asset.css');

        $this->doValidTagTest($tag, 'test_optional_asset');
    }

    public function getRequireTwigAsset()
    {
        return array(
            array(new RequireScriptTag('asset_source_path')),
            array(new RequireStyleTag('asset_source_path')),
        );
    }
}
