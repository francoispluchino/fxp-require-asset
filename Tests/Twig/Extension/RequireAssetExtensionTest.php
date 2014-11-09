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

        $scriptAsset = $this->getMock('Assetic\Asset\AssetInterface');
        $scriptAsset
            ->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnValue('acemodemo/js/asset.js'));
        $scriptAsset
            ->expects($this->any())
            ->method('getVars')
            ->will($this->returnValue(array()));
        $scriptAsset
            ->expects($this->any())
            ->method('getValues')
            ->will($this->returnValue(array()));

        $styleAsset = $this->getMock('Assetic\Asset\AssetInterface');
        $styleAsset
            ->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnValue('acemodemo/css/asset.css'));
        $styleAsset
            ->expects($this->any())
            ->method('getVars')
            ->will($this->returnValue(array()));
        $styleAsset
            ->expects($this->any())
            ->method('getValues')
            ->will($this->returnValue(array()));

        /* @var AssetInterface $scriptAsset */
        $manager->set(Utils::formatName('@acme_demo/js/asset.js'), $scriptAsset);
        /* @var AssetInterface $styleAsset */
        $manager->set(Utils::formatName('@acme_demo/css/asset.css'), $styleAsset);

        $this->ext->container = $container;
        $this->doValidTagTest($tag);
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
}
