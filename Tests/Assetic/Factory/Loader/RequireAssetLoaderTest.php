<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Assetic\Factory\Loader;

use Assetic\Factory\Resource\ResourceInterface;
use Fxp\Component\RequireAsset\Assetic\Factory\Loader\RequireAssetLoader;
use Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResource;

/**
 * Require Asset Loader Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadWithRequireAssetResource()
    {
        /* @var RequireAssetResource|\PHPUnit_Framework_MockObject_MockObject $resource */
        $resource = $this->getMockBuilder('Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResource')
            ->disableOriginalConstructor()
            ->getMock();

        $loader = new RequireAssetLoader();
        $valid = array('RESOURCE_FORMULAE_CONFIG');

        $resource->expects($this->any())
            ->method('getFormulae')
            ->will($this->returnValue($valid));

        $this->assertSame($valid, $loader->load($resource));
    }

    public function testLoadWithResource()
    {
        /* @var ResourceInterface|\PHPUnit_Framework_MockObject_MockObject $resource */
        $resource = $this->getMock('Assetic\Factory\Resource\ResourceInterface');
        $loader = new RequireAssetLoader();

        $this->assertSame(array(), $loader->load($resource));
    }
}
