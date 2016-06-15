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
 */
class CommonAssetExtensionTest extends AbstractAssetExtensionTest
{
    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\Twig\AlreadyExistTagPositionException
     */
    public function testTagPositionIsAlreadyIncluded()
    {
        $this->ext->createTagPosition('category', 'type');
        $this->ext->createTagPosition('category', 'type');
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\Twig\MissingTagPositionException
     */
    public function testContentIsNotRenderingBecauseTheTagPositionIsMissing()
    {
        $tag = $this->getMockBuilder('Fxp\Component\RequireAsset\Tag\TagInterface')->getMock();
        $tag
            ->expects($this->any())
            ->method('getTagPositionName')
            ->will($this->returnValue('category:type:position'));

        /* @var TagInterface $tag */
        $this->ext->addTag($tag);
        $this->ext->renderTags();
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\Twig\RuntimeTagRendererException
     * @expectedExceptionMessage No template tag renderer has been found for the "category_type" tag
     */
    public function testAssetRendererNotFound()
    {
        $ext = new AssetExtension();
        $tag = $this->getMockBuilder('Fxp\Component\RequireAsset\Tag\TagInterface')->getMock();
        $tag
            ->expects($this->any())
            ->method('getTagPositionName')
            ->will($this->returnValue('category:type:position'));
        $tag
            ->expects($this->any())
            ->method('getCategory')
            ->will($this->returnValue('category'));
        $tag
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('type'));

        /* @var TagInterface $tag */
        echo $ext->createTagPosition('category', 'type', -1, null, 'position');
        $ext->addTag($tag);
        $ext->renderTags();
    }

    /**
     * @dataProvider getTwigTags
     *
     * @param string $tag
     *
     * @expectedException \Twig_Error_Syntax
     * @expectedExceptionMessageRegExp /^The attribute name "(\w+)" must be an NAME, STRING/
     */
    public function testInvalidTypeAttributeName($tag)
    {
        $this->getTemplate($tag, 'invalid_attr_type.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     *
     * @param string $tag
     *
     * @expectedException \Twig_Error_Syntax
     * @expectedExceptionMessageRegExp /^The attribute value "([w\/]+)" must be an NAME, STRING, NUMBER/
     */
    public function testInvalidTypeAttributeValue($tag)
    {
        $this->getTemplate($tag, 'invalid_attr_value.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     *
     * @param string $tag
     *
     * @expectedException \Twig_Error_Syntax
     * @expectedExceptionMessageRegExp /^Invalid type for attribute "(\w+)". Expected /
     */
    public function testInvalidConfigTypeAttributeValue($tag)
    {
        $this->getTemplate($tag, 'invalid_attr_value_config.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     *
     * @param string $tag
     *
     * @expectedException \Twig_Error_Syntax
     * @expectedExceptionMessageRegExp /^The attribute "(\w+)" does not exist for the "(\w+)" tag. Only attributes "([A-Za-z0-9_, "]+)" are available/
     */
    public function testInvalidAttributeName($tag)
    {
        $this->getTemplate($tag, 'invalid_attr_name.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     *
     * @param string $tag
     *
     * @expectedException \Twig_Error_Syntax
     * @expectedExceptionMessage must be followed by "=" operator
     */
    public function testInvalidAttributeOperator($tag)
    {
        $this->getTemplate($tag, 'invalid_attr_operator.html.twig');
    }
}
