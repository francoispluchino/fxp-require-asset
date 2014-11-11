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

use Assetic\Asset\AssetInterface;
use Fxp\Component\RequireAsset\Assetic\Util\Utils;
use Fxp\Component\RequireAsset\Tag\RequireScriptTag;
use Fxp\Component\RequireAsset\Tag\RequireStyleTag;

/**
 * Require Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetExtensionTest extends AbstractAssetExtensionTest
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
     * @param string $tag
     */
    public function testAssetIsNotManagedByAsseticManager($tag)
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\Twig\RequireTagException', 'is not managed by the Assetic Manager');

        $this->doValidTagTest($tag);
    }

    /**
     * @dataProvider getRequireTwigTags
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
     * @param string $tag
     */
    public function testTwigTagsWithoutAssetInTag($tag)
    {
        $this->setExpectedException('\Twig_Error_Syntax', sprintf('The twig tag "%s" require a lest one asset', $tag));
        $this->doValidTagTest($tag, 'test_without_asset');
    }

    public function getRequireTwigAsset()
    {
        return array(
            array(new RequireScriptTag('asset_source_path')),
            array(new RequireStyleTag('asset_source_path')),
        );
    }

    /**
     * Add require asset in assetic manager.
     *
     * @param string $source
     * @param string $target
     */
    protected function addAsset($source, $target)
    {
        $asset = $this->getMock('Assetic\Asset\AssetInterface');
        $asset
            ->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnValue($target));
        $asset
            ->expects($this->any())
            ->method('getVars')
            ->will($this->returnValue(array()));
        $asset
            ->expects($this->any())
            ->method('getValues')
            ->will($this->returnValue(array()));

        /* @var AssetInterface $asset */
        $this->manager->set(Utils::formatName($source), $asset);
    }
}
