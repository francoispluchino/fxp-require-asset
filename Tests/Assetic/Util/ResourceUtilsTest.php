<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Assetic\Util;

use Fxp\Component\RequireAsset\Assetic\Util\ResourceUtils;

/**
 * Resource Utils Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class ResourceUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function getDebugMode()
    {
        return array(
            array(true,  array('filter1', 'filter3')),
            array(false, array('filter1', 'filter2', 'filter3')),
        );
    }

    /**
     * @dataProvider getDebugMode
     */
    public function testCleanDebugFilters($debug, array $valid)
    {
        $filters = array(
            'filter1',
            '?filter2',
            'filter3',
        );

        $this->assertSame($valid, ResourceUtils::cleanDebugFilters($filters, $debug));
    }
}
