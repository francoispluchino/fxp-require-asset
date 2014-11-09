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
 * Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getInlineTwigTags()
    {
        return array(
            array('inline_script'),
            array('inline_style'),
        );
    }

    /**
     * @return array
     */
    public function getRequireTwigTags()
    {
        return array(
            array('require_script'),
            array('require_style'),
        );
    }

    /**
     * @return array
     */
    public function getTwigTags()
    {
        return array_merge(
            $this->getInlineTwigTags(),
            $this->getRequireTwigTags()
        );
    }

    public function testTagPositionIsAlreadyIncluded()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\Twig\AlreadyExistAssetPositionException');

        $ext = new AssetExtension();
        $ext->createAssetPosition('category', 'type');
        $ext->createAssetPosition('category', 'type');
    }

    public function testContentIsNotRenderingBecauseTheAssetPositionIsMissing()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\Twig\MissingAssetPositionException');

        $ext = new AssetExtension();
        $asset = $this->getMock('Fxp\Component\RequireAsset\Twig\Asset\TwigAssetInterface');
        $asset
            ->expects($this->any())
            ->method('getTagPositionName')
            ->will($this->returnValue('category:type:position'));

        /* @var TwigAssetInterface $asset */
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

    /**
     * @dataProvider getInlineTwigTags
     * @param string $tag
     */
    public function testInlineEmptyBody($tag)
    {
        $this->getTemplate($tag, 'empty_body.html.twig');
    }

    /**
     * @dataProvider getInlineTwigTags
     * @param string $tag
     */
    public function testTwigTags($tag)
    {
        $tpl = $this->getTemplate($tag, 'test.html.twig');
        $content = $tpl->render(array());
        $valid = file_get_contents(__DIR__.'/../../Fixtures/Resources/views/' . $tag . '/test.valid.template');
        $valid = str_replace("\r", "", $valid);

        $this->assertEquals(mb_convert_encoding($valid, 'utf8'), $content);
    }

    /**
     * @param string $tag
     * @param string $file
     *
     * @return \Twig_TemplateInterface
     */
    protected function getTemplate($tag, $file)
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../../Fixtures/Resources/views/' . $tag);
        $twig = new \Twig_Environment($loader, array('debug' => true, 'cache' => false));
        $twig->addExtension(new AssetExtension());

        return $twig->loadTemplate($file);
    }
}
