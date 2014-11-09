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
    public function getTwigTags()
    {
        return array(
            array('inline_script'),
            array('inline_style'),
        );
    }

    public function testTagPositionIsAlreadyIncluded()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\Twig\AlreadyExistAssetPositionException');

        $ext = new AssetExtension();
        $ext->createAssetPosition('inline', 'script');
        $ext->createAssetPosition('inline', 'script');
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
    public function testTwigTags($tag)
    {
        $tpl = $this->getTemplate($tag . '.html.twig');
        $content = $tpl->render(array());
        $valid = file_get_contents(__DIR__.'/../../Fixtures/Resources/views/' . $tag . '.valid.template');
        $valid = str_replace("\r", "", $valid);

        $this->assertEquals(mb_convert_encoding($valid, 'utf8'), $content);
    }

    /**
     * @dataProvider getTwigTags
     * @param string $tag
     */
    public function testInlineEmptyBody($tag)
    {
        $this->getTemplate($tag . '_empty_body.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     * @param string $tag
     */
    public function testInvalidAttributeType($tag)
    {
        $this->setExpectedException('Twig_Error_Syntax');
        $this->getTemplate($tag . '_invalid_attr_type.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     * @param string $tag
     */
    public function testInvalidAttributeName($tag)
    {
        $this->setExpectedException('Twig_Error_Syntax');
        $this->getTemplate($tag . '_invalid_attr_name.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     * @param string $tag
     */
    public function testInvalidAttributeOperator($tag)
    {
        $this->setExpectedException('Twig_Error_Syntax');
        $this->getTemplate($tag . '_invalid_attr_operator.html.twig');
    }

    /**
     * @dataProvider getTwigTags
     * @param string $tag
     */
    public function testInvalidAttributeValue($tag)
    {
        $this->setExpectedException('Twig_Error_Syntax');
        $this->getTemplate($tag . '_invalid_attr_value.html.twig');
    }

    /**
     * @param $file
     *
     * @return \Twig_TemplateInterface
     */
    protected function getTemplate($file)
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../../Fixtures/Resources/views');
        $twig = new \Twig_Environment($loader, array('debug' => true, 'cache' => false));
        $twig->addExtension(new AssetExtension());

        return $twig->loadTemplate($file);
    }
}
