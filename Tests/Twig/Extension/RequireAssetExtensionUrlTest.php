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

use Fxp\Component\RequireAsset\Asset\Config\LocaleManagerInterface;
use Fxp\Component\RequireAsset\Asset\RequireAssetManagerInterface;
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
     * @var RequireAssetManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    /**
     * @var LocaleManagerInterface
     */
    protected $localeManager;

    protected function setUp()
    {
        $this->manager = $this->getMockBuilder(RequireAssetManagerInterface::class)->getMock();
        $this->ext = new RequireAssetExtension($this->manager);
    }

    protected function tearDown()
    {
        $this->ext = null;
        $this->manager = null;
        $this->localeManager = null;
    }

    public function testRequireAsset()
    {
        $this->manager->expects($this->at(0))
            ->method('has')
            ->with('@acme_demo/js/asset.js')
            ->willReturn(true);

        $this->manager->expects($this->at(1))
            ->method('getPath')
            ->with('@acme_demo/js/asset.js')
            ->willReturn('/assets/acemodemo/js/asset.js');

        $this->manager->expects($this->at(2))
            ->method('has')
            ->with('@acme_demo/js/asset2.js')
            ->willReturn(false);

        $this->assertCount(1, $this->ext->getFunctions());
        $this->assertSame('/assets/acemodemo/js/asset.js', $this->ext->requireAsset('@acme_demo/js/asset.js'));
        $this->assertSame('@acme_demo/js/asset2.js', $this->ext->requireAsset('@acme_demo/js/asset2.js'));
    }
}
