<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Asset\HttpAsset;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Fxp\Component\RequireAsset\Assetic\Factory\Loader\RequireAssetLoader;
use Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResource;
use Fxp\Component\RequireAsset\Assetic\Filter\RequireCssRewriteFilter;
use Fxp\Component\RequireAsset\Assetic\Util\Utils;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Require CSS Rewrite Filter Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireCssRewriteFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AssetFactory
     */
    protected $af;

    /**
     * @var LazyAssetManager
     */
    protected $lam;

    /**
     * @var RequireCssRewriteFilter
     */
    protected $rcrf;

    protected function setUp()
    {
        $this->af = new AssetFactory('web');
        $this->lam = new LazyAssetManager($this->af);
        $this->rcrf = new RequireCssRewriteFilter($this->lam);
    }

    protected function teanDown()
    {
        $this->af = null;
        $this->lam = null;
        $fs = new Filesystem();
        $fs->remove($this->getCacheDir());
    }

    protected function getCacheDir()
    {
        return sys_get_temp_dir().'/fxp_require_asset-require-css-rewrite-filter-test';
    }

    protected function createFixtures(array $fixtures)
    {
        $fs = new Filesystem();

        foreach ($fixtures as $filename => $content) {
            $fs->dumpFile($this->getCacheDir().'/'.$filename, $content);
        }
    }

    public function testFilterLoad()
    {
        /* @var AssetInterface $asset */
        $asset = $this->getMockBuilder('Assetic\Asset\AssetInterface')->getMock();

        $this->rcrf->filterLoad($asset);
    }

    public function testFilterDumpWithoutAsseticResource()
    {
        $asset = new FileAsset($this->getCacheDir().'/foobar/asset.css');

        $this->rcrf->filterDump($asset);
    }

    public function getDataForFilterDump()
    {
        return array(
            array('url("https://example.tld/web/assets/img/bg.jpg")', 'url("https://example.tld/web/assets/img/bg.jpg")'),
            array('url("/web/assets/img/bg.jpg")',                    'url("/web/assets/img/bg.jpg")'),
            array('url("../../static/img/bg.jpg")',                   'url("../img/bg.jpg")'),
            array('url("../../static/img/bg.jpg?foo=bar")',           'url("../img/bg.jpg?foo=bar")'),
        );
    }

    /**
     * @dataProvider getDataForFilterDump
     */
    public function testFilterDumpWithAsseticResource($sourceUrl, $newUrl)
    {
        $cssContent = 'h1 { background: %s; }';

        $this->lam->setLoader('requireassetloader', new RequireAssetLoader());
        $fixtures = array(
            'foobar/static/img/bg.jpg' => array(
                'target' => 'web/assets/img/bg.jpg',
                'content' => '',
            ),
            'foobar/src/css/asset.css' => array(
                'target' => 'web/assets/css/asset.css',
                'content' => sprintf($cssContent, $sourceUrl),
            ),
        );

        foreach ($fixtures as $filename => $config) {
            $path = $this->getCacheDir().'/'.$filename;
            $rar = new RequireAssetResource(Utils::formatName($filename), $path, $config['target']);

            $this->createFixtures(array($filename => $config['content']));
            $this->lam->addResource($rar, 'requireassetloader');

            $asset = new FileAsset($path);
            $asset->dump();

            if (strrpos($filename, '.css') === (strlen($filename) - 4)) {
                $this->rcrf->filterDump($asset);

                $this->assertSame(sprintf($cssContent, $newUrl), $asset->getContent());
            }
        }
    }

    public function getDataForFilterDumpWithUrl()
    {
        return array(
            array('url("https://example.tld/web/assets/img/bg.jpg")', 'url("https://example.tld/web/assets/img/bg.jpg")'),
            array('url("/foobar/static/img/bg.jpg")',                 'url("https://foobar.tld/foobar/static/img/bg.jpg")'),
            array('url("../../static/img/bg.jpg")',                   'url("../img/bg.jpg")'),
            array('url("../../static/img/bg.jpg?foo=bar")',           'url("../img/bg.jpg?foo=bar")'),
        );
    }

    /**
     * @dataProvider getDataForFilterDumpWithUrl
     */
    public function testFilterDumpWithUrlAsseticResource($sourceUrl, $newUrl)
    {
        $cssContent = 'h1 { background: %s; }';

        $this->lam->setLoader('requireassetloader', new RequireAssetLoader());
        $fixtures = array(
            'https://foobar.tld/foobar/static/img/bg.jpg' => array(
                'target' => 'web/assets/img/bg.jpg',
                'content' => '',
            ),
            'https://foobar.tld/foobar/src/css/asset.css' => array(
                'target' => 'web/assets/css/asset.css',
                'content' => sprintf($cssContent, $sourceUrl),
            ),
        );

        foreach ($fixtures as $filename => $config) {
            $path = $filename;
            $rar = new RequireAssetResource(Utils::formatName($filename), $path, $config['target']);

            $this->lam->addResource($rar, 'requireassetloader');

            $asset = new HttpAsset($path);
            $asset->setContent($config['content']);

            if (strrpos($filename, '.css') === (strlen($filename) - 4)) {
                $this->rcrf->filterDump($asset);

                $this->assertSame(sprintf($cssContent, $newUrl), $asset->getContent());
            }
        }
    }

    public function getDataForFilterDumpWithPartialUrl()
    {
        return array(
            array('url("https://example.tld/web/assets/img/bg.jpg")', 'url("https://example.tld/web/assets/img/bg.jpg")'),
            array('url("/foobar/static/img/bg.jpg")',                 'url("https://foobar.tld/foobar/static/img/bg.jpg")'),
            array('url("../../static/img/bg.jpg")',                   'url("../../static/img/bg.jpg")'),
            array('url("../../static/img/bg.jpg?foo=bar")',           'url("../../static/img/bg.jpg?foo=bar")'),
        );
    }

    /**
     * @dataProvider getDataForFilterDumpWithPartialUrl
     */
    public function testFilterDumpWithPartialUrlAsseticResource($sourceUrl, $newUrl)
    {
        $cssContent = 'h1 { background: %s; }';

        $this->lam->setLoader('requireassetloader', new RequireAssetLoader());
        $fixtures = array(
            'https://foobar.tld/foobar/src/css/asset.css' => array(
                'target' => 'web/assets/css/asset.css',
                'content' => sprintf($cssContent, $sourceUrl),
            ),
        );

        foreach ($fixtures as $filename => $config) {
            $path = $filename;
            $rar = new RequireAssetResource(Utils::formatName($filename), $path, $config['target']);

            $this->lam->addResource($rar, 'requireassetloader');

            $asset = new HttpAsset($path);
            $asset->setContent($config['content']);

            if (strrpos($filename, '.css') === (strlen($filename) - 4)) {
                $this->rcrf->filterDump($asset);

                $this->assertSame(sprintf($cssContent, $newUrl), $asset->getContent());
            }
        }
    }

    public function testGetHash()
    {
        $this->assertSame(spl_object_hash($this->rcrf), $this->rcrf->hash());
    }
}
