<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Factory\LazyAssetManager;
use Assetic\Filter\FilterInterface;
use Assetic\Util\CssUtils;
use Fxp\Bundle\RequireAssetBundle\Assetic\Factory\Resource\RequireAssetResource;
use Fxp\Bundle\RequireAssetBundle\Assetic\Util\FilterUtils;

/**
 * Fixes relative CSS urls with require output rewrite.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireCssRewriteFilter implements FilterInterface
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
        $sourceBase = $asset->getSourceRoot();
        $sourcePath = $asset->getSourcePath();
        $sourceFile = realpath($sourceBase . DIRECTORY_SEPARATOR . $asset->getSourcePath());
        $paths = $this->getResourcePaths();

        if (!isset($paths[$sourceFile])) {
            return;
        }

        $content = $this->getContent($asset, $paths, $sourceBase, $sourcePath, $sourceFile);
        $asset->setContent($content);
    }

    /**
     * Get the asset content.
     *
     * @param AssetInterface $asset
     * @param array          $paths      The resource paths
     * @param string         $sourceBase The source base
     * @param string         $sourcePath The source path
     * @param string         $sourceFile The source filename
     *
     * @return string The asset content
     */
    protected function getContent(AssetInterface $asset, array $paths, $sourceBase, $sourcePath, $sourceFile)
    {
        $manager = $this->manager;
        $host = $this->getHost($sourceBase, $sourcePath);
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
                $resources[realpath($resource->getSourcePath())] = $resource->getName();
            }
        }

        return $resources;
    }

    /**
     * Get the host.
     *
     * @param string $sourceBase The source base
     * @param string $sourcePath The source path
     *
     * @return string
     */
    protected function getHost($sourceBase, $sourcePath)
    {
        // learn how to get from the target back to the source
        if (false !== strpos($sourceBase, '://')) {
            list($scheme, $url) = explode('://', $sourceBase.'/'.$sourcePath, 2);
            list($host,) = explode('/', $url, 2);

            return $scheme.'://'.$host.'/';
        }

        return '';
    }
}
