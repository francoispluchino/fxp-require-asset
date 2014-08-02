<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Tests\DependencyInjection;

use Fxp\Bundle\RequireAssetBundle\DependencyInjection\FxpRequireAssetExtension;
use Fxp\Bundle\RequireAssetBundle\FxpRequireAssetBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Bundle Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FxpRequireAssetExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testCompileContainerWithExtension()
    {
        $container = $this->getContainer();
        $this->assertTrue($container->hasDefinition('twig.extension.fxp_require_asset.embed_asset'));
    }

    /**
     * Gets the container.
     *
     * @return ContainerBuilder
     */
    protected function getContainer()
    {
        $container = new ContainerBuilder();
        $bundle = new FxpRequireAssetBundle();
        $bundle->build($container); // Attach all default factories

        $extension = new FxpRequireAssetExtension();
        $container->registerExtension($extension);
        $config = array();
        $extension->load(array($config), $container);

        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}
