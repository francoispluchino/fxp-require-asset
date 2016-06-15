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
use Fxp\Component\RequireAsset\Assetic\Config\FileExtensionManager;
use Fxp\Component\RequireAsset\Assetic\Config\PackageManager;
use Fxp\Component\RequireAsset\Assetic\Config\PatternManager;

/**
 * Assetic Package Manager Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class PackageManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileExtensionManager
     */
    protected $fem;

    /**
     * @var PatternManager
     */
    protected $ptm;

    /**
     * @var PackageManager
     */
    protected $pm;

    protected function setUp()
    {
        $this->fem = new FileExtensionManager();
        $this->ptm = new PatternManager();
        $this->pm = new PackageManager($this->fem, $this->ptm);
    }

    protected function tearDown()
    {
        $this->fem = null;
        $this->ptm = null;
        $this->pm = null;
    }

    public function testBasic()
    {
        $this->pm->addPackage('package1', 'source_path1');
        $this->pm->addPackages(array(
            'package2' => array('source_path' => 'source_path2'),
            'package3' => array('source_path' => 'source_path3'),
        ));

        $this->assertTrue($this->pm->hasPackage('package1'));
        $this->assertTrue($this->pm->hasPackage('package2'));
        $this->assertTrue($this->pm->hasPackage('package3'));

        $this->pm->removePackage('package2');

        $this->assertTrue($this->pm->hasPackage('package1'));
        $this->assertFalse($this->pm->hasPackage('package2'));
        $this->assertTrue($this->pm->hasPackage('package3'));

        $validPackage1 = new ConfigPackage('package1', 'source_path1');
        $validPackage3 = new ConfigPackage('package3', 'source_path3');
        $valid = array(
            'package1' => $validPackage1->getPackage(),
            'package3' => $validPackage3->getPackage(),
        );

        $this->assertEquals($valid, $this->pm->getPackages());
        $this->assertEquals($valid['package1'], $this->pm->getPackage('package1'));
        $this->assertEquals($valid['package3'], $this->pm->getPackage('package3'));
    }

    public function testAddPackagesWithArrayConfig()
    {
        $this->pm->addPackages(array(
            'package1' => array('source_path' => 'source_path1'),
            'package2' => array('source_path' => 'source_path2'),
        ));

        $validPackage1 = new ConfigPackage('package1', 'source_path1');
        $validPackage2 = new ConfigPackage('package2', 'source_path2');
        $valid = array(
            'package1' => $validPackage1->getPackage(),
            'package2' => $validPackage2->getPackage(),
        );
        $this->assertEquals($valid, $this->pm->getPackages());
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\BadMethodCallException
     */
    public function testAddPackageWithLockedManager()
    {
        $this->assertSame(array(), $this->pm->getPackages());

        $this->pm->addPackage('package');
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\BadMethodCallException
     */
    public function testAddPackagesWithLockedManager()
    {
        $this->assertSame(array(), $this->pm->getPackages());

        $this->pm->addPackages(array('package'));
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\BadMethodCallException
     */
    public function testRemovePackageWithLockedManager()
    {
        $this->assertSame(array(), $this->pm->getPackages());

        $this->pm->removePackage('package');
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\InvalidConfigurationException
     */
    public function testGetPackageWithNonexistingPackage()
    {
        $this->assertSame(array(), $this->pm->getPackages());

        $this->pm->getPackage('package');
    }
}
