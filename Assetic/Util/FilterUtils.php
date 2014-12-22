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

use Assetic\Factory\LazyAssetManager;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Assetic Filter Utils.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class FilterUtils
{
    /**
     * Filter the css references.
     *
     * @param LazyAssetManager $manager    The asset manager
     * @param array            $paths      The resource paths
     * @param string           $sourceBase The source base
     * @param string           $targetBase The target base
     * @param array            $matches    The regex matches
     * @param string           $host       The host
     *
     * @return string
     */
    public static function filterCssReferences(LazyAssetManager $manager, array $paths, $sourceBase, $targetBase, array $matches, $host)
    {
        if (false !== strpos($matches['url'], '://') || 0 === strpos($matches['url'], '//') || 0 === strpos($matches['url'], 'data:')) {
            // absolute or protocol-relative or data uri
            return (string) $matches[0];
        }

        if (isset($matches['url'][0]) && '/' === $matches['url'][0]) {
            // root relative
            return (string) str_replace($matches['url'], rtrim($host, '/').'/'.ltrim($matches['url'], '/'), $matches[0]);
        }

        // document relative
        return static::getReferenceUrl($manager, $paths, $sourceBase, $targetBase, $matches['url'], $matches[0]);
    }

    /**
     * Fix the real path.
     *
     * @param string $path
     *
     * @return string
     */
    public static function fixRealPath($path)
    {
        $rPath = realpath($path);

        return is_string($rPath) ? $rPath : $path;
    }

    /**
     * Get the reference url.
     *
     * @param LazyAssetManager $manager    The asset manager
     * @param array            $paths      The resource paths
     * @param string           $sourceBase The source base
     * @param string           $targetBase The target base
     * @param string           $url        The relative url
     * @param string           $content    The full content of matched pattern
     *
     * @return string The new url
     */
    protected static function getReferenceUrl(LazyAssetManager $manager, array $paths, $sourceBase, $targetBase, $url, $content)
    {
        $search = $url;
        $fullPath = static::getRealPath($sourceBase, $url);
        $target = $targetBase.'/'.$url;

        if (isset($paths[$fullPath])) {
            $urlOptions = substr(basename($url), strlen(basename($fullPath)));
            $target = $manager->get($paths[$fullPath])->getTargetPath().$urlOptions;
        }

        return str_replace($search, static::getRealTargetUrl($targetBase, $target), $content);
    }

    /**
     * Get the real relative url.
     *
     * @param string $targetBase The target base of asset
     * @param string $target     The target of asset
     *
     * @return string
     */
    protected static function getRealTargetUrl($targetBase, $target)
    {
        $fs = new Filesystem();
        $targetBase = $fs->makePathRelative(dirname($target), $targetBase);
        $url = $targetBase.basename($target);

        if (0 === strpos($url, './')) {
            $url = substr($url, 2);
        }

        return $url;
    }

    /**
     * Get the real path of resource.
     *
     * @param string $sourceBase The source base
     * @param string $url        The relative url
     *
     * @return string
     */
    protected static function getRealPath($sourceBase, $url)
    {
        $fs = new Filesystem();
        $path = $fs->isAbsolutePath($url) ? static::fixRealPath($url) : $sourceBase.'/'.$url;
        $path = static::cleanPath($path, '?');
        $path = static::cleanPath($path, '#');
        $path = static::fixRealPath($path);
        $path = static::getVirtualRealPath($path);

        return $path;
    }

    /**
     * Remove the options in path.
     *
     * @param string $path The path
     * @param string $key  The url options key
     *
     * @return string
     */
    protected static function cleanPath($path, $key = '?')
    {
        $pos = strpos($path, $key);

        if (false !== $pos) {
            $path = substr($path, 0, $pos);
        }

        return $path;
    }

    /**
     * Convert the path to virtual real path.
     *
     * @param string $path The path contained '../'
     *
     * @return string
     */
    protected static function getVirtualRealPath($path)
    {
        if (false !== strpos($path, '../')) {
            do {
                $pos = strpos($path, '../');
                $pathBase = substr($path, 0, $pos);
                $pathBase = dirname($pathBase);
                $path = $pathBase.'/'.substr($path, $pos + 3);
            } while (false !== strpos($path, '../'));
        }

        return $path;
    }
}
