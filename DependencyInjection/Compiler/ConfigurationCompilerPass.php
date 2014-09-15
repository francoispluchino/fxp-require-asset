<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Override the config by the global custom config.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class ConfigurationCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        /* @var ParameterBag $pb */
        $pb = $container->getParameterBag();

        $this->configureFileExtensionManager($container, $pb);
        $this->configurePatternManager($container, $pb);
        $this->configurePackageManager($container, $pb);
    }

    /**
     * Configure the default file extentions section.
     *
     * @param ContainerBuilder $container
     * @param ParameterBag     $pb
     */
    protected function configureFileExtensionManager(ContainerBuilder $container, ParameterBag $pb)
    {
        $def = $container->getDefinition('fxp_require_asset.assetic.config.file_extension_manager');
        $exts = $container->getParameter('fxp_require_asset.assetic.config.file_extensions');

        $def->addMethodCall('addDefaultExtensions', array($exts));
        $pb->remove('fxp_require_asset.assetic.config.file_extensions');
    }

    /**
     * Configure the default pattern section.
     *
     * @param ContainerBuilder $container
     * @param ParameterBag     $pb
     */
    protected function configurePatternManager(ContainerBuilder $container, ParameterBag $pb)
    {
        $def = $container->getDefinition('fxp_require_asset.assetic.config.pattern_manager');
        $patterns = $container->getParameter('fxp_require_asset.assetic.config.patterns');

        $def->addMethodCall('addDefaultPatterns', array($patterns));
        $pb->remove('fxp_require_asset.assetic.config.patterns');
    }

    /**
     * Configure the asset package section.
     *
     * @param ContainerBuilder $container
     * @param ParameterBag     $pb
     */
    protected function configurePackageManager(ContainerBuilder $container, ParameterBag $pb)
    {
        $def = $container->getDefinition('fxp_require_asset.assetic.config.package_manager');
        $packages = $container->getParameter('fxp_require_asset.assetic.config.packages');

        $def->addMethodCall('addPackages', array($packages));
        $pb->remove('fxp_require_asset.assetic.config.packages');
    }
}
