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
    public function testTagPositionIsAlreadyIncluded()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\Twig\AlreadyExistTagPositionException');

        $this->ext->createTagPosition('category', 'type');
        $this->ext->createTagPosition('category', 'type');
    }

    public function testContentIsNotRenderingBecauseTheTagPositionIsMissing()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\Twig\MissingTagPositionException');

        $tag = $this->getMock('Fxp\Component\RequireAsset\Tag\TagInterface');
        $tag
            ->expects($this->any())
            ->method('getTagPositionName')
            ->will($this->returnValue('category:type:position'));

        /* @var TagInterface $tag */
        $this->ext->addTag($tag);
        $this->ext->renderTags();
    }

    public function testAssetRendererNotFound()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\Twig\RuntimeTagRendererException', 'No template tag renderer has been found for the "category_type" tag');

        $ext = new AssetExtension();
        $tag = $this->getMock('Fxp\Component\RequireAsset\Tag\TagInterface');
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
        $ext->createTagPosition('category', 'type', -1, null, 'position');
        $ext->addTag($tag);
        $ext->renderTags();
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
