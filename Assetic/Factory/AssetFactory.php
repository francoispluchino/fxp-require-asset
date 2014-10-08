<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Assetic\Factory;

use Fxp\Bundle\RequireAssetBundle\Assetic\Asset;
use Fxp\Bundle\RequireAssetBundle\Assetic\AssetInterface;
use Fxp\Bundle\RequireAssetBundle\Assetic\Config\PackageInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Factory of asset definition.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AssetFactory
{
    /**
     * Creates the asset definition.
     *
     * @param PackageInterface $package      The asset package instance
     * @param SplFileInfo      $file         The Spo file info instance
     * @param string           $outputPrefix The output prefix
     * @param Filesystem|null  $fs           The filesystem instance
     *
     * @return AssetInterface
     */
    public static function create(PackageInterface $package, SplFileInfo $file, $outputPrefix = '', Filesystem $fs = null)
    {
        $fs = null !== $fs ? $fs : new Filesystem();
        $target = $file->getLinkTarget();
        $source = realpath($package->getSourcePath());
        $output = rtrim($fs->makePathRelative($target, $source), '/');
        $name = $package->getName() . '/' . $output;
        $options = array();
        $filters = array();
        $ext = $file->getExtension();
        $output = $package->getSourceBase() . '/' . $output;
        $output = trim($outputPrefix, '/') . '/' . $output;

        if ($package->hasExtension($ext)) {
            $extt = $package->getExtension($ext);
            $options = $extt->getOptions();
            $filters = $extt->getFilters();
        }

        return new Asset($name, $options, $filters, $target, $output);
    }
}
