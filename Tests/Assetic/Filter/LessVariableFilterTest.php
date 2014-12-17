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

use Assetic\Asset\StringAsset;
use Fxp\Component\RequireAsset\Assetic\Filter\LessVariableFilter;

/**
 * Less Variable Filter Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class LessVariableFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetContent()
    {
        $content = '@content = "content";';
        $packages = array(
            '@asset/package1'     => 'path_to_package1',
            '@asset/package2'     => 'path_to_package2',
            'vendor_asset_bundle' => 'path_to_bundle',
        );
        $filter = new LessVariableFilter($packages);
        $asset = new StringAsset($content, array($filter));
        $asset->dump();

        $validContent = '@asset-package1-path: "path_to_package1";'.PHP_EOL
            .'@asset-package2-path: "path_to_package2";'.PHP_EOL
            .'@vendor-asset-bundle-path: "path_to_bundle";'.PHP_EOL
            .$content;

        $this->assertEquals($validContent, $asset->getContent());
    }
}
