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

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds all assets in bundle located in 'Resources/assets'.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class BundleAssetsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $packageManagerDef = $container->getDefinition('fxp_require_asset.assetic.config.package_manager');
        $bundles = $container->getParameter('kernel.bundles');

        foreach ($bundles as $name => $class) {
            $ref = new \ReflectionClass($class);
            $path = realpath(dirname($ref->getFileName()) . '/Resources/assets');

            if ($path) {
                $package = array(
                    'name'        => Container::underscore($name),
                    'source_path' => $path,
                );
                $packageManagerDef->addMethodCall('addPackage', array($package));
            }
        }
    }
}
