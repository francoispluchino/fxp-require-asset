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

use Fxp\Component\RequireAsset\Asset\Config\AssetReplacementManager;
use Fxp\Component\RequireAsset\Asset\Config\AssetReplacementManagerInterface;
use Fxp\Component\RequireAsset\Asset\Config\LocaleManagerInterface;
use Fxp\Component\RequireAsset\Twig\Extension\AssetExtension;
use Fxp\Component\RequireAsset\Twig\Extension\CoreAssetExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

/**
 * Abstract Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractAssetExtensionTest extends TestCase
{
    /**
     * @var AssetExtension
     */
    protected $ext;

    /**
     * @var LocaleManagerInterface
     */
    protected $localeManager;

    /**
     * @var AssetReplacementManagerInterface
     */
    protected $replacementManager;

    /**
     * @var array
     */
    protected $debugCommonAssets = [];

    protected function setUp(): void
    {
        $this->replacementManager = new AssetReplacementManager();
        $this->ext = new CoreAssetExtension(
            realpath(__DIR__.'/../../Fixtures/Webpack/assets.json'),
            'assets',
            $this->localeManager,
            $this->replacementManager
        );
    }

    protected function tearDown(): void
    {
        $this->ext = null;
        $this->replacementManager = null;
        $this->localeManager = null;
    }

    /**
     * @return array
     */
    public function getInlineTwigTags()
    {
        return [
            ['inline_script'],
            ['inline_style'],
        ];
    }

    /**
     * @return array
     */
    public function getRequireTwigTags()
    {
        return [
            ['require_script'],
            ['require_style'],
        ];
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
     * @param string $testFile
     * @param string $validSuffix
     */
    public function doValidTagTest($tag, $testFile = 'test', $validSuffix = ''): void
    {
        $tpl = $this->getTemplate($tag, $testFile.'.html.twig');
        $content = $tpl->render([]);
        $valid = file_get_contents(__DIR__.'/../../Fixtures/Resources/views/'.$tag.'/'.$testFile.$validSuffix.'.valid.template');
        $valid = str_replace("\r", '', $valid);

        $this->assertEquals(mb_convert_encoding($valid, 'utf8'), $content);
    }

    /**
     * @param string $tag
     * @param string $file
     *
     * @return TemplateWrapper
     */
    protected function getTemplate($tag, $file)
    {
        $loader = new FilesystemLoader(__DIR__.'/../../Fixtures/Resources/views/'.$tag);
        $twig = new Environment($loader, ['debug' => true, 'cache' => false]);
        $twig->addExtension($this->ext);

        return $twig->load($file);
    }
}
