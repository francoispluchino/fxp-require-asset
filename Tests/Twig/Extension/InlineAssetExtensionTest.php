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

use Fxp\Component\RequireAsset\Twig\Extension\InlineAssetExtension;

/**
 * Inline Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InlineAssetExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $file
     *
     * @return \Twig_TemplateInterface
     */
    protected function getTemplate($file)
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../../Fixtures/Resources/views');
        $twig = new \Twig_Environment($loader, array('debug' => true, 'cache' => false));
        $twig->addExtension(new InlineAssetExtension());

        return $twig->loadTemplate($file);
    }

    public function testAssetStylesheets()
    {
        $tpl = $this->getTemplate('asset_css.html.twig');
        $content = $tpl->render(array());
        $valid = file_get_contents(__DIR__.'/../../Fixtures/Resources/views/asset_css.valid.template');
        $valid = str_replace("\r", "", $valid);

        $this->assertEquals(mb_convert_encoding($valid, 'utf8'), $content);
    }

    public function testAssetJavascripts()
    {
        $tpl = $this->getTemplate('asset_js.html.twig');
        $content = $tpl->render(array());
        $valid = file_get_contents(__DIR__.'/../../Fixtures/Resources/views/asset_js.valid.template');
        $valid = str_replace("\r", "", $valid);

        $this->assertEquals(mb_convert_encoding($valid, 'utf8'), $content);
    }

    public function testInvalidAssetType()
    {
        $ext = new InlineAssetExtension();

        $this->setExpectedException('Twig_Error_Runtime');
        $ext->addAsset('invalid', array(), array(), array());
    }

    public function testWrongAssetCallable()
    {
        $ext = new InlineAssetExtension();

        $ext->addAsset('javascript', array(), array(), array());

        $this->setExpectedException('Twig_Error_Runtime');
        $ext->renderInlineAssets();
    }

    public function testEmptyBody()
    {
        $this->getTemplate('asset_empty_body.html.twig');
    }

    public function testInvalidNameType()
    {
        $this->setExpectedException('Twig_Error_Syntax');
        $this->getTemplate('asset_invalid_name_type.html.twig');
    }

    public function testInvalidAttributeName()
    {
        $this->setExpectedException('Twig_Error_Syntax');
        $this->getTemplate('asset_invalid_attr_name.html.twig');
    }

    public function testInvalidOperator()
    {
        $this->setExpectedException('Twig_Error_Syntax');
        $this->getTemplate('asset_invalid_operator.html.twig');
    }

    public function testInvalidValue()
    {
        $this->setExpectedException('Twig_Error_Syntax');
        $this->getTemplate('asset_invalid_value.html.twig');
    }
}
