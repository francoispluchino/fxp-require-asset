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
use PHPUnit\Framework\TestCase;

/**
 * Abstract Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 *
 * @internal
 */
final class RequireAssetExtensionUrlTest extends TestCase
{
    /**
     * @var RequireAssetExtension
     */
    protected $ext;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequireAssetManagerInterface
     */
    protected $manager;

    /**
     * @var LocaleManagerInterface
     */
    protected $localeManager;

    protected function setUp(): void
    {
        $this->manager = $this->getMockBuilder(RequireAssetManagerInterface::class)->getMock();
        $this->ext = new RequireAssetExtension($this->manager);
    }

    protected function tearDown(): void
    {
        $this->ext = null;
        $this->manager = null;
        $this->localeManager = null;
    }

    public function testRequireAsset(): void
    {
        $this->manager->expects($this->at(0))
            ->method('has')
            ->with('@webpack/asset')
            ->willReturn(true)
        ;

        $this->manager->expects($this->at(1))
            ->method('getPath')
            ->with('@webpack/asset')
            ->willReturn('/assets/asset.js')
        ;

        $this->manager->expects($this->at(2))
            ->method('has')
            ->with('@webpack/asset2')
            ->willReturn(false)
        ;

        $this->assertCount(1, $this->ext->getFunctions());
        $this->assertSame('/assets/asset.js', $this->ext->requireAsset('@webpack/asset'));
        $this->assertSame('@webpack/asset2', $this->ext->requireAsset('@webpack/asset2'));
    }
}
