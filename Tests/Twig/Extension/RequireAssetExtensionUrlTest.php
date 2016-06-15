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
use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Fxp\Component\RequireAsset\Assetic\Config\LocaleManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Util\Utils;
use Fxp\Component\RequireAsset\Twig\Extension\RequireAssetExtension;

/**
 * Abstract Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetExtensionUrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequireAssetExtension
     */
    protected $ext;

    /**
     * @var LazyAssetManager
     */
    protected $manager;

    /**
     * @var AssetFactory
     */
    protected $factory;

    /**
     * @var LocaleManagerInterface
     */
    protected $localeManager;

    protected function setUp()
    {
        $this->factory = new AssetFactory('web');
        $this->manager = new LazyAssetManager($this->factory);
        $this->factory->setAssetManager($this->manager);
        $this->ext = new RequireAssetExtension($this->manager);
    }

    protected function tearDown()
    {
        $this->ext = null;
        $this->manager = null;
        $this->factory = null;
        $this->localeManager = null;
    }

    public function testRequireAsset()
    {
        $this->addAsset('@acme_demo/js/asset.js', '/assets/acemodemo/js/asset.js');

        $this->assertSame('fxp_require_asset_url', $this->ext->getName());
        $this->assertCount(1, $this->ext->getFunctions());
        $this->assertSame('/assets/acemodemo/js/asset.js', $this->ext->requireAsset('@acme_demo/js/asset.js'));
        $this->assertSame('@acme_demo/js/asset2.js', $this->ext->requireAsset('@acme_demo/js/asset2.js'));
    }

    /**
     * Add require asset in assetic manager.
     *
     * @param string $source
     * @param string $target
     */
    protected function addAsset($source, $target)
    {
        $asset = $this->getMockBuilder('Assetic\Asset\AssetInterface')->getMock();
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
