<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic\Util;

use Fxp\Component\RequireAsset\Assetic\Config\AssetResource;
use Fxp\Component\RequireAsset\Assetic\Config\LocaleManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\OutputManagerInterface;
use Fxp\Component\RequireAsset\Assetic\Config\PackageInterface;
use Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResourceInterface;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Config Asset Resource Utils.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AssetResourceUtils
{
    /**
     * Create the config asset resource.
     *
     * @param string $name      The require asset name
     * @param string $classname The classname
     * @param array  $arguments The arguments
     *
     * @return AssetResource
     */
    public static function createAssetResource($name, $classname, array $arguments)
    {
        return new AssetResource($name, $classname, 'fxp_require_asset_loader', $arguments);
    }

    /**
     * Creates the asset resource.
     *
     * @param PackageInterface       $package       The asset package instance
     * @param SplFileInfo            $file          The Spo file info instance
     * @param OutputManagerInterface $outputManager The output manager
     *
     * @return AssetResource
     */
    public static function createAssetResourceByPackage(PackageInterface $package, SplFileInfo $file, OutputManagerInterface $outputManager)
    {
        $classname = 'Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResource';
        $args = ResourceUtils::createConfigResource($package, $file, $outputManager);

        return static::createAssetResource($args[0], $classname, $args);
    }

    /**
     * Create the locale common asset resource.
     *
     * @param RequireAssetResourceInterface $resource      The require resource
     * @param string                        $locale        The locale
     * @param LocaleManagerInterface        $localeManager The locale manager
     *
     * @return AssetResource
     */
    public static function createLocaleAssetResource(RequireAssetResourceInterface $resource, $locale, LocaleManagerInterface $localeManager)
    {
        $localeInputs = LocaleUtils::getLocaleCommonInputs($resource->getInputs(), $locale, $localeManager);
        $name = LocaleUtils::formatLocaleCommonName($resource->getPrettyName(), $locale);
        $targetPath = LocaleUtils::convertLocaleTartgetPath($resource->getTargetPath(), $locale);
        $classname = 'Fxp\Component\RequireAsset\Assetic\Factory\Resource\CommonRequireAssetResource';
        $args = array($name, $localeInputs, $targetPath, $resource->getFilters(), $resource->getOptions());

        return static::createAssetResource($name, $classname, $args);
    }
}
