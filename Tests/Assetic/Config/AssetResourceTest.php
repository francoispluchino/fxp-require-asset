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

use Fxp\Component\RequireAsset\Assetic\Config\AssetResource;

/**
 * Asset Resource Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetResourceTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $this->setExpectedException('Fxp\Component\RequireAsset\Exception\InvalidConfigurationException', 'The "DateTime" class must extends the "Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResourceInterface" interface');

        $ar = new AssetResource('now', 'DateTime', 'loader', array('name'), 0);

        $ar->getNewInstance();
    }
}
