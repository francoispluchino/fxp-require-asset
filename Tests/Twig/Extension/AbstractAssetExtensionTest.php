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

use Assetic\AssetManager;
use Fxp\Component\RequireAsset\Twig\Extension\AssetExtension;
use Fxp\Component\RequireAsset\Twig\Extension\CoreAssetExtension;

/**
 * Abstract Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractAssetExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AssetExtension
     */
    protected $ext;

    /**
     * @var AssetManager
     */
    protected $manager;

    protected function setUp()
    {
        $this->manager = new AssetManager();
        $this->ext = new CoreAssetExtension($this->manager);
    }

    protected function tearDown()
    {
        $this->ext = null;
        $this->manager = null;
    }

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
        $twig->addExtension($this->ext);

        return $twig->loadTemplate($file);
    }

    /**
     * @param string $tag
     * @param string $testFile
     */
    public function doValidTagTest($tag, $testFile = 'test')
    {
        $tpl = $this->getTemplate($tag, $testFile . '.html.twig');
        $content = $tpl->render(array());
        $valid = file_get_contents(__DIR__.'/../../Fixtures/Resources/views/' . $tag . '/' . $testFile . '.valid.template');
        $valid = str_replace("\r", "", $valid);

        $this->assertEquals(mb_convert_encoding($valid, 'utf8'), $content);
    }
}