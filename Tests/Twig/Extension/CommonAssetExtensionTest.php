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

use Fxp\Component\RequireAsset\Tag\TagInterface;
use Fxp\Component\RequireAsset\Twig\Extension\AssetExtension;

/**
 * Common Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 *
 * @internal
 */
final class CommonAssetExtensionTest extends AbstractAssetExtensionTest
{
    public function testTagPositionIsAlreadyIncluded(): void
    {
        $this->expectException(\Fxp\Component\RequireAsset\Exception\Twig\AlreadyExistTagPositionException::class);

        $this->ext->createTagPosition('category', 'type');
        $this->ext->createTagPosition('category', 'type');
    }

    public function testContentIsNotRenderingBecauseTheTagPositionIsMissing(): void
    {
        $this->expectException(\Fxp\Component\RequireAsset\Exception\Twig\MissingTagPositionException::class);

        $tag = $this->getMockBuilder('Fxp\Component\RequireAsset\Tag\TagInterface')->getMock();
        $tag
            ->expects($this->any())
            ->method('getTagPositionName')
            ->will($this->returnValue('category:type:position'))
        ;
        $tag
            ->expects($this->any())
            ->method('getTemplateLine')
            ->willReturn(-1)
        ;

        /* @var TagInterface $tag */
        $this->ext->addTag($tag);
        $this->ext->renderTags();
    }

    public function testAssetRendererNotFound(): void
    {
        $this->expectException(\Fxp\Component\RequireAsset\Exception\Twig\RuntimeTagRendererException::class);
        $this->expectExceptionMessage('No template tag renderer has been found for the "category_type" tag');

        $ext = new AssetExtension();
        $tag = $this->getMockBuilder('Fxp\Component\RequireAsset\Tag\TagInterface')->getMock();
        $tag
            ->expects($this->any())
            ->method('getTagPositionName')
            ->will($this->returnValue('category:type:position'))
        ;
        $tag
            ->expects($this->any())
            ->method('getCategory')
            ->will($this->returnValue('category'))
        ;
        $tag
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('type'))
        ;

        /* @var TagInterface $tag */
        echo $ext->createTagPosition('category', 'type', -1, null, 'position');
        $ext->addTag($tag);
        $ext->renderTags();
    }

    public function testAssetRendererNotFoundForRequireTag(): void
    {
        $this->expectException(\Fxp\Component\RequireAsset\Exception\Twig\RuntimeTagRendererException::class);
        $this->expectExceptionMessage('No template tag renderer has been found for the "category_type" tag with the asset "ASSET_PATH"');

        $ext = new AssetExtension();
        $tag = $this->getMockBuilder('Fxp\Component\RequireAsset\Tag\RequireTagInterface')->getMock();
        $tag
            ->expects($this->any())
            ->method('getTagPositionName')
            ->will($this->returnValue('category:type:position'))
        ;
        $tag
            ->expects($this->any())
            ->method('getCategory')
            ->will($this->returnValue('category'))
        ;
        $tag
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('type'))
        ;
        $tag
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('ASSET_PATH'))
        ;

        /* @var TagInterface $tag */
        echo $ext->createTagPosition('category', 'type', -1, null, 'position');
        $ext->addTag($tag);
        $ext->renderTags();
    }

    /**
     * @dataProvider getTwigTags
     *
     * @param string $tag
     */
    public function testInvalidTypeAttributeName($tag): void
    {
        $this->expectException(\Twig_Error_Syntax::class);
        $this->expectExceptionMessageRegExp('/^The attribute name "(\\w+)" must be an NAME, STRING/');

        $this->getTemplate($tag, 'invalid_attr_type.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     *
     * @param string $tag
     */
    public function testInvalidTypeAttributeValue($tag): void
    {
        $this->expectException(\Twig_Error_Syntax::class);
        $this->expectExceptionMessageRegExp('/^The attribute value "([w\\/]+)" must be an NAME, STRING, NUMBER/');

        $this->getTemplate($tag, 'invalid_attr_value.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     *
     * @param string $tag
     */
    public function testInvalidConfigTypeAttributeValue($tag): void
    {
        $this->expectException(\Twig_Error_Syntax::class);
        $this->expectExceptionMessageRegExp('/^Invalid type for attribute "(\\w+)". Expected /');

        $this->getTemplate($tag, 'invalid_attr_value_config.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     *
     * @param string $tag
     */
    public function testInvalidAttributeName($tag): void
    {
        $this->expectException(\Twig_Error_Syntax::class);
        $this->expectExceptionMessageRegExp('/^The attribute "(\\w+)" does not exist for the "(\\w+)" tag. Only attributes "([A-Za-z0-9_, "]+)" are available/');

        $this->getTemplate($tag, 'invalid_attr_name.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     *
     * @param string $tag
     */
    public function testInvalidAttributeOperator($tag): void
    {
        $this->expectException(\Twig_Error_Syntax::class);
        $this->expectExceptionMessage('must be followed by "=" operator');

        $this->getTemplate($tag, 'invalid_attr_operator.html.twig');
    }
}
