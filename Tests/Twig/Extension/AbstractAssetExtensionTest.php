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

use Fxp\Component\RequireAsset\Twig\Extension\AssetExtension;

/**
 * Abstract Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractAssetExtensionTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @param string $tag
     */
    public function doValidTagTest($tag)
    {
        $tpl = $this->getTemplate($tag, 'test.html.twig');
        $content = $tpl->render(array());
        $valid = file_get_contents(__DIR__.'/../../Fixtures/Resources/views/' . $tag . '/test.valid.template');
        $valid = str_replace("\r", "", $valid);

        $this->assertEquals(mb_convert_encoding($valid, 'utf8'), $content);
    }
}
