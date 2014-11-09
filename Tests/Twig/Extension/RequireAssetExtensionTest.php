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
use Assetic\Factory\LazyAssetManager;
use Fxp\Component\RequireAsset\Assetic\Util\Utils;
use Fxp\Component\RequireAsset\Twig\Asset\RequireScriptTwigAsset;
use Fxp\Component\RequireAsset\Twig\Asset\RequireStyleTwigAsset;
use Fxp\Component\RequireAsset\Twig\Asset\TwigAssetInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;

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
     * @dataProvider getContainerServiceConfig
     *
     * @param string $tag
     * @param bool   $useContainer
     * @param bool   $useAssetic
     * @param bool   $useHelper
     * @param string $message
     */
    public function testInvalidContainerServce($tag, $useContainer, $useAssetic, $useHelper, $message)
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\Twig\RequireAssetException', sprintf($message, $tag));

        if ($useContainer) {
            $this->ext->container = $this->getContainer($useAssetic, $useHelper);
        }

        $this->doValidTagTest($tag);
    }

    /**
     * @dataProvider getRequireTwigTags
     * @param string $tag
     */
    public function testAssetIsNotManagedByAsseticManager($tag)
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\Twig\RequireAssetException', 'is not managed by the Assetic Manager');

        $this->ext->container = $this->getContainer();
        $this->doValidTagTest($tag);
    }

    /**
     * @dataProvider getRequireTwigTags
     * @param string $tag
     */
    public function testTwigTags($tag)
    {
        $container = $this->getContainer();
        $manager = $container->get('assetic.asset_manager');

        $this->addAsset($manager, '@acme_demo/js/asset.js', 'acemodemo/js/asset.js');
        $this->addAsset($manager, '@acme_demo/css/asset.css', 'acemodemo/css/asset.css');

        $this->ext->container = $container;
        $this->doValidTagTest($tag);
    }

    /**
     * @dataProvider getRequireTwigTags
     * @param string $tag
     */
    public function testTwigTagsWithMultiAsset($tag)
    {
        $container = $this->getContainer();
        $manager = $container->get('assetic.asset_manager');

        $this->addAsset($manager, '@acme_demo/js/asset.js', 'acemodemo/js/asset.js');
        $this->addAsset($manager, '@acme_demo/js/asset2.js', 'acemodemo/js/asset2.js');
        $this->addAsset($manager, '@acme_demo/css/asset.css', 'acemodemo/css/asset.css');
        $this->addAsset($manager, '@acme_demo/css/asset2.css', 'acemodemo/css/asset2.css');

        $this->ext->container = $container;
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
            array(new RequireScriptTwigAsset('asset_source_path')),
            array(new RequireStyleTwigAsset('asset_source_path')),
        );
    }

    /**
     * @dataProvider getRequireTwigAsset
     * @param TwigAssetInterface $asset
     */
    public function testWrongInlineScriptCallable(TwigAssetInterface $asset)
    {
        $msg = sprintf('The conditional render is required for the %s asset "%s"', $asset->getCategory(), 'asset_source_path');
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\Twig\AssetRenderException', $msg);

        $asset->render(null);
    }

    /**
     * Gets the container.
     *
     * @param bool $useAssetic
     * @param bool $useHelper
     *
     * @return ContainerBuilder
     */
    protected function getContainer($useAssetic = true, $useHelper = true)
    {
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.debug'       => false,
            'kernel.environment' => 'test',
            'kernel.name'        => 'kernel',
            'kernel.root_dir'    => __DIR__,
            'kernel.charset'     => 'UTF-8',
            'assetic.debug'      => false,
        )));

        if ($useAssetic) {
            $asseticFactory = new Definition('Assetic\Factory\AssetFactory');
            $asseticFactory->addArgument('web');
            $container->setDefinition('assetic.asset_factory', $asseticFactory);

            $asseticManager = new Definition('Assetic\Factory\LazyAssetManager');
            $asseticManager->addArgument(new Reference('assetic.asset_factory'));
            $container->setDefinition('assetic.asset_manager', $asseticManager);
        }

        if ($useHelper) {
            $package = new Definition($this->getMockClass('Symfony\Component\Templating\Asset\PackageInterface'));
            $container->setDefinition('templating.asset.default_package', $package);
            $helper = new Definition('Symfony\Component\Templating\Helper\CoreAssetsHelper');
            $helper->addArgument(new Reference('templating.asset.default_package'));
            $container->setDefinition('templating.helper.assets', $helper);
        }

        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        if ($useHelper) {
            $packageId = 'templating.asset.default_package';
            $packageDefault = $container->get($packageId);
            $packageDefault
                ->expects($this->any())
                ->method('getUrl')
                ->will($this->returnCallback(function ($value) {
                    return '/assets/' . $value;
                }));
        }

        return $container;
    }

    /**
     * Add require asset in assetic manager.
     *
     * @param LazyAssetManager $manager
     * @param string           $source
     * @param string           $target
     */
    protected function addAsset(LazyAssetManager $manager, $source, $target)
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
        $manager->set(Utils::formatName($source), $asset);
    }
}
