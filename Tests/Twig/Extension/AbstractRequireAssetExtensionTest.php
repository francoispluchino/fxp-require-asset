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

use Assetic\Asset\AssetInterface;
use Fxp\Component\RequireAsset\Asset\Util\AssetUtils;

/**
 * Abstract Require Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractRequireAssetExtensionTest extends AbstractAssetExtensionTest
{
    /**
     * Add require asset in assetic manager.
     *
     * @param string $source
     * @param string $target
     */
    protected function addAsset($source, $target)
    {
        $asset = $this->getMockBuilder('Assetic\Asset\AssetInterface')->getMock();
        $asset
            ->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnValue($target));
        $asset
            ->expects($this->any())
            ->method('getVars')
            ->will($this->returnValue([]));
        $asset
            ->expects($this->any())
            ->method('getValues')
            ->will($this->returnValue([]));

        /* @var AssetInterface $asset */
        $this->manager->set(AssetUtils::formatName($source), $asset);
    }

    /**
     * Add formulae asset in assetic manager.
     *
     * @param string $name
     * @param array  $inputs
     * @param string $target
     * @param bool   $isCommonAsset
     */
    protected function addFormulaeAsset($name, array $inputs, $target, $isCommonAsset = true)
    {
        $config = [
            [],
            [],
            [
                'fxp_require_common_asset' => $isCommonAsset,
                'output' => $target,
            ],
        ];

        foreach ($inputs as $input) {
            $config[0][] = '@'.AssetUtils::formatName($input);
        }

        $this->manager->setFormula($name, $config);
    }
}
