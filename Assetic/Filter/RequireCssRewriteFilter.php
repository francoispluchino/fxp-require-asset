<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Factory\LazyAssetManager;
use Assetic\Filter\FilterInterface;
use Assetic\Filter\HashableInterface;
use Assetic\Util\CssUtils;
use Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResource;
use Fxp\Component\RequireAsset\Assetic\Util\FilterUtils;

/**
 * Fixes relative CSS urls with require output rewrite.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireCssRewriteFilter implements FilterInterface, HashableInterface
{
    /**
     * @var LazyAssetManager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param LazyAssetManager $manager
     */
    public function __construct(LazyAssetManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        // no action
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        $sourceFile = FilterUtils::fixRealPath($asset->getSourceRoot().'/'.$asset->getSourcePath());
        $paths = $this->getResourcePaths();

        if (!isset($paths[$sourceFile])) {
            return;
        }

        $content = $this->getContent($asset, $paths, dirname($sourceFile), $sourceFile);
        $asset->setContent($content);
    }

    /**
     * {@inheritdoc}
     */
    public function hash()
    {
        return spl_object_hash($this);
    }

    /**
     * Get the asset content.
     *
     * @param AssetInterface $asset
     * @param array          $paths      The resource paths
     * @param string         $sourceBase The source base
     * @param string         $sourceFile The source filename
     *
     * @return string The asset content
     */
    protected function getContent(AssetInterface $asset, array $paths, $sourceBase, $sourceFile)
    {
        $manager = $this->manager;
        $host = $this->getHost($sourceFile);
        $targetBase = dirname($manager->get($paths[$sourceFile])->getTargetPath());

        return CssUtils::filterReferences($asset->getContent(), function ($matches) use ($manager, $paths, $sourceBase, $targetBase, $host) {
            return FilterUtils::filterCssReferences($manager, $paths, $sourceBase, $targetBase, $matches, $host);
        });
    }

    /**
     * Get the map of resource paths and assetic name.
     *
     * @return array
     */
    protected function getResourcePaths()
    {
        $resources = array();

        foreach ($this->manager->getResources() as $resource) {
            if ($resource instanceof RequireAssetResource) {
                $resources[FilterUtils::fixRealPath($resource->getSourcePath())] = $resource->getPrettyName();
            }
        }

        return $resources;
    }

    /**
     * Get the host.
     *
     * @param string $sourceFile The source file
     *
     * @return string
     */
    protected function getHost($sourceFile)
    {
        // learn how to get from the target back to the source
        if (false !== strpos($sourceFile, '://')) {
            list($scheme, $url) = explode('://', $sourceFile, 2);
            list($host,) = explode('/', $url, 2);

            return $scheme.'://'.$host.'/';
        }

        return '';
    }
}
