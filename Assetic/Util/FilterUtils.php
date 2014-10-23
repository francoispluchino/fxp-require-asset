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

        if (isset($matches['url'][0]) && '/' == $matches['url'][0]) {
            // root relative
            return (string) str_replace($matches['url'], $host . $matches['url'], $matches[0]);
        }

        // document relative
        return static::getReferenceUrl($manager, $paths, $sourceBase, $targetBase, $matches['url']);
    }

    /**
     * Get the reference url.
     *
     * @param LazyAssetManager $manager    The asset manager
     * @param array            $paths      The resource paths
     * @param string           $sourceBase The source base
     * @param string           $targetBase The target base
     * @param string           $url        The relative url
     *
     * @return string The new url
     */
    protected static function getReferenceUrl(LazyAssetManager $manager, array $paths, $sourceBase, $targetBase, $url)
    {
        $fullpath = static::getRealPath($sourceBase, $url);

        if (isset($paths[$fullpath])) {
            $fs = new Filesystem();
            $target = $manager->get($paths[$fullpath])->getTargetPath();
            $targetBase = $fs->makePathRelative(dirname($target), $targetBase);
            $urlOptions = substr(basename($url), strlen(basename($fullpath)));
            $url = $targetBase . basename($target) . $urlOptions;
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
        $path = $sourceBase . DIRECTORY_SEPARATOR . $url;
        $path = static::cleanPath($path, '?');
        $path = static::cleanPath($path, '#');

        return realpath($path);
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
}
