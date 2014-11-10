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

use Fxp\Component\RequireAsset\Twig\Asset\TwigAssetInterface;
use Fxp\Component\RequireAsset\Twig\Extension\AssetExtension;

/**
 * Common Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class CommonAssetExtensionTest extends AbstractAssetExtensionTest
{
    public function testTagPositionIsAlreadyIncluded()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\Twig\AlreadyExistAssetPositionException');

        $this->ext->createAssetPosition('category', 'type');
        $this->ext->createAssetPosition('category', 'type');
    }

    public function testContentIsNotRenderingBecauseTheAssetPositionIsMissing()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\Twig\MissingAssetPositionException');

        $asset = $this->getMock('Fxp\Component\RequireAsset\Twig\Asset\TwigAssetInterface');
        $asset
            ->expects($this->any())
            ->method('getTagPositionName')
            ->will($this->returnValue('category:type:position'));

        /* @var TwigAssetInterface $asset */
        $this->ext->addAsset($asset);
        $this->ext->renderAssets();
    }

    public function testAssetRendererNotFound()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\Twig\AssetRendererException', 'No twig asset renderer has been found for the "category_type" asset');

        $ext = new AssetExtension();
        $asset = $this->getMock('Fxp\Component\RequireAsset\Twig\Asset\TwigAssetInterface');
        $asset
            ->expects($this->any())
            ->method('getTagPositionName')
            ->will($this->returnValue('category:type:position'));
        $asset
            ->expects($this->any())
            ->method('getCategory')
            ->will($this->returnValue('category'));
        $asset
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('type'));

        /* @var TwigAssetInterface $asset */
        $ext->createAssetPosition('category', 'type', -1, null, 'position');
        $ext->addAsset($asset);
        $ext->renderAssets();
    }

    /**
     * @dataProvider getTwigTags
     * @param string $tag
     */
    public function testInvalidTypeAttributeName($tag)
    {
        $this->setExpectedExceptionRegExp('Twig_Error_Syntax', '/^The attribute name "(\w+)" must be an NAME, STRING/');
        $this->getTemplate($tag, 'invalid_attr_type.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     * @param string $tag
     */
    public function testInvalidTypeAttributeValue($tag)
    {
        $this->setExpectedExceptionRegExp('Twig_Error_Syntax', '/^The attribute value "([w\/]+)" must be an NAME, STRING, NUMBER/');
        $this->getTemplate($tag, 'invalid_attr_value.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     * @param string $tag
     */
    public function testInvalidConfigTypeAttributeValue($tag)
    {
        $this->setExpectedExceptionRegExp('Twig_Error_Syntax', '/^Invalid type for attribute "(\w+)". Expected /');
        $this->getTemplate($tag, 'invalid_attr_value_config.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     * @param string $tag
     */
    public function testInvalidAttributeName($tag)
    {
        $this->setExpectedExceptionRegExp('Twig_Error_Syntax', '/^The attribute "(\w+)" does not exist for the "(\w+)" tag. Only attributes "([A-Za-z0-9_, "]+)" are available/');
        $this->getTemplate($tag, 'invalid_attr_name.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     * @param string $tag
     */
    public function testInvalidAttributeOperator($tag)
    {
        $this->setExpectedException('Twig_Error_Syntax', 'must be followed by "=" operator');
        $this->getTemplate($tag, 'invalid_attr_operator.html.twig');
    }
}
