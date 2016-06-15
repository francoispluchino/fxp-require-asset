<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Assetic\Config;

use Fxp\Component\RequireAsset\Assetic\Config\ConfigPackage;
use Fxp\Component\RequireAsset\Assetic\Config\Package;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Package Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class PackageTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        $this->cleanFixtures();
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\InvalidArgumentException
     */
    public function testInvalidSourcePath()
    {
        new Package(new ConfigPackage('NAME'));
    }

    public function testBasic()
    {
        $cpkg = new ConfigPackage('NAME', 'SOURCE_PATH', 'SOURCE_BASE');
        $cpkg->addExtension('ext1');
        $cpkg->addPattern('pattern1');

        $pkg = new Package($cpkg);

        $this->assertSame($cpkg->getName(), $pkg->getName());
        $this->assertSame($cpkg->getSourcePath(), $pkg->getSourcePath());
        $this->assertSame($cpkg->getSourceBase(), $pkg->getSourceBase());
        $this->assertTrue($pkg->hasExtension('ext1'));
        $this->assertSame($cpkg->getExtension('ext1'), $pkg->getExtension('ext1'));
        $this->assertSame($cpkg->getExtensions(), $pkg->getExtensions());
        $this->assertTrue($pkg->hasPattern('pattern1'));
        $this->assertSame($cpkg->getPatterns(), $pkg->getPatterns());
    }

    public function testWithoutSourceBase()
    {
        $cpkg = new ConfigPackage('NAME', 'path/to/source');
        $pkg = new Package($cpkg);

        $this->assertSame('source', $pkg->getSourceBase());
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\InvalidConfigurationException
     */
    public function testNonExistentFileExtension()
    {
        $cpkg = new ConfigPackage('NAME', 'SOURCE_PATH');
        $pkg = new Package($cpkg);
        $pkg->getExtension('ext1');
    }

    public function testClone()
    {
        $cpkg = new ConfigPackage('NAME', 'path/to/source');
        $cpkg->addExtension('ext');
        $pkg = new Package($cpkg);

        $pkg2 = clone $pkg;

        $this->assertEquals($pkg, $pkg2);
        $this->assertNotSame($pkg, $pkg2);
        $this->assertEquals($pkg->getExtensions(), $pkg2->getExtensions());
        $this->assertNotSame($pkg->getExtensions(), $pkg2->getExtensions());
    }

    public function getDebugMode()
    {
        return array(
            array(false),
            array(true),
        );
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\InvalidArgumentException
     */
    public function testGetFilesWithInvalidSourcePath()
    {
        $cpkg = new ConfigPackage('NAME', 'SOURCE_PATH');
        $pkg = new Package($cpkg);

        $pkg->getFiles();
    }

    /**
     * @dataProvider getDebugMode
     */
    public function testGetFiles($debug)
    {
        static::createFixtures();

        $cpkg = new ConfigPackage('NAME', $this->getFixturesDir().'/foobar');
        $cpkg
            ->addExtension('js')
            ->addExtension('css')
            ->addExtension('less')
            ->addExtension('map', array(), array(), null, true)
            ->addExtension('md', array(), array(), null, false, true)
            ->addPattern('*')
            ->addPattern('!.*')
            ->addPattern('dist/css/*')
            ->addPattern('!dist/js/*')
            ->addPattern('!dist/fonts/*')
            ->addPattern('!Gruntfile.js');

        $pkg = new Package($cpkg);
        $fs = new Filesystem();
        $base = realpath($this->getFixturesDir());
        $files = array();

        /* @var SplFileInfo $file */
        foreach ($pkg->getFiles($debug) as $file) {
            $files[] = rtrim($fs->makePathRelative($file->getRealpath(), $base), '/');
        }

        $valid = array(
            'foobar/dist/css/foobar-theme.css',
            'foobar/dist/css/foobar-theme.min.css',
            'foobar/dist/css/foobar.css',
            'foobar/dist/css/foobar.min.css',
            'foobar/js/component-a.js',
            'foobar/js/component-b.js',
            'foobar/less/component-a.less',
            'foobar/less/component-b.less',
            'foobar/less/foobar-theme.less',
            'foobar/less/foobar.less',
            'foobar/less/mixins.less',
            'foobar/less/variable.less',
        );

        if ($debug) {
            $valid = array_merge($valid, array(
                'foobar/dist/css/foobar.css.map',
                'foobar/dist/css/foobar-theme.css.map',
            ));
        }

        sort($valid);
        sort($files);

        $this->assertSame($valid, $files);
    }

    public static function createFixtures()
    {
        $fs = new Filesystem();

        foreach (static::getFixtureFiles() as $filename) {
            $fs->dumpFile(static::getFixturesDir().'/'.$filename, '');
        }
    }

    public static function cleanFixtures()
    {
        if (file_exists(static::getFixturesDir())) {
            $fs = new Filesystem();
            $fs->remove(static::getFixturesDir());
        }
    }

    /**
     * @return string
     */
    public static function getFixturesDir()
    {
        return sys_get_temp_dir().'/fxp-require-asset-fixtures';
    }

    /**
     * @return array
     */
    public static function getFixtureFiles()
    {
        return array(
            'foobar/bower.json',
            'foobar/package.json',
            'foobar/README.md',
            'foobar/CONTRIBUTING.MD',
            'foobar/CNAME',
            'foobar/Gruntfile.js',
            'foobar/.travis.yml',
            'foobar/.gitignore',
            'foobar/.gitattributes',
            'foobar/dist/js/foobar.js',
            'foobar/dist/js/foobar.min.js',
            'foobar/dist/css/foobar.css',
            'foobar/dist/css/foobar.css.map',
            'foobar/dist/css/foobar.min.css',
            'foobar/dist/css/foobar-theme.css',
            'foobar/dist/css/foobar-theme.css.map',
            'foobar/dist/css/foobar-theme.min.css',
            'foobar/dist/fonts/font-family-regular.eot',
            'foobar/dist/fonts/font-family-regular.svg',
            'foobar/dist/fonts/font-family-regular.ttf',
            'foobar/dist/fonts/font-family-regular.woff',
            'foobar/doc/sitmap.xml',
            'foobar/doc/robot.txt',
            'foobar/doc/index.html',
            'foobar/fonts/font-family-regular.eot',
            'foobar/fonts/font-family-regular.svg',
            'foobar/fonts/font-family-regular.ttf',
            'foobar/fonts/font-family-regular.woff',
            'foobar/js/.jscsrc',
            'foobar/js/.jshintrc',
            'foobar/js/component-a.js',
            'foobar/js/component-b.js',
            'foobar/less/foobar.less',
            'foobar/less/foobar-theme.less',
            'foobar/less/variable.less',
            'foobar/less/mixins.less',
            'foobar/less/component-a.less',
            'foobar/less/component-b.less',
        );
    }
}
