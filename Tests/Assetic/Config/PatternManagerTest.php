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

use Fxp\Component\RequireAsset\Assetic\Config\PatternManager;

/**
 * Pattern Manager Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class PatternManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $pm = new PatternManager();

        $pm->addDefaultPattern('pattern1/*');
        $pm->addDefaultPatterns(array(
            'pattern2/*',
            'pattern3/*',
        ));

        $this->assertTrue($pm->hasDefaultPattern('pattern1/*'));
        $this->assertTrue($pm->hasDefaultPattern('pattern2/*'));
        $this->assertTrue($pm->hasDefaultPattern('pattern3/*'));
        $this->assertFalse($pm->hasDefaultPattern('nonexistingpattern'));

        $pm->removeDefaultPattern('pattern2/*');

        $this->assertTrue($pm->hasDefaultPattern('pattern1/*'));
        $this->assertFalse($pm->hasDefaultPattern('pattern2/*'));
        $this->assertTrue($pm->hasDefaultPattern('pattern3/*'));

        $valid = array(
            'pattern1/*',
            'pattern3/*',
        );
        $this->assertSame($valid, $pm->getDefaultPatterns());
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\BadMethodCallException
     */
    public function testAddDefaultPatternWithLockedManager()
    {
        $pm = new PatternManager();
        $this->assertSame(array(), $pm->getDefaultPatterns());

        $pm->addDefaultPattern('pattern');
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\BadMethodCallException
     */
    public function testAddDefaultPatternsWithLockedManager()
    {
        $pm = new PatternManager();
        $this->assertSame(array(), $pm->getDefaultPatterns());

        $pm->addDefaultPatterns(array('pattern'));
    }

    /**
     * @expectedException \Fxp\Component\RequireAsset\Exception\BadMethodCallException
     */
    public function testAddRemovePatternWithLockedManager()
    {
        $pm = new PatternManager();
        $this->assertSame(array(), $pm->getDefaultPatterns());

        $pm->removeDefaultPattern('pattern');
    }
}
