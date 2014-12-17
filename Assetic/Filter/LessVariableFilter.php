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

use Assetic\Filter\FilterInterface;
use Assetic\Asset\AssetInterface;

/**
 * Add the variables of require asset package directories at the beginning of the less file.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class LessVariableFilter implements FilterInterface
{
    /**
     * @var array
     */
    protected $packages;

    /**
     * @var string|null
     */
    private $cache;

    /**
     * Constructor.
     *
     * @param array $packages The map of asset package name and path
     */
    public function __construct(array $packages = array())
    {
        $this->packages = $packages;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        $content = $this->getContent().$asset->getContent();
        $asset->setContent($content);
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        // no action
    }

    /**
     * Get the content.
     *
     * @return string
     */
    private function getContent()
    {
        if (null === $this->cache) {
            $this->cache = $this->getContentAssetPackagePaths($this->packages);
        }

        return $this->cache;
    }

    /**
     * get content variables of the asset package paths.
     *
     * @param array $packages The asset package paths
     *
     * @return string
     */
    protected function getContentAssetPackagePaths(array $packages)
    {
        $content = '';

        foreach ($packages as $name => $path) {
            $content .= $this->dumpVariable($name, $path, '-path').PHP_EOL;
        }

        return $content;
    }

    /**
     * Dump the variable.
     *
     * @param string $name   The variable name
     * @param string $value  The variable value
     * @param string $suffix The suffix of the variable name
     *
     * @return string
     */
    protected function dumpVariable($name, $value, $suffix = '')
    {
        $name = strtolower(str_replace(array('.', '_', '/'), '-', $name));
        $name = trim($name, '@');

        return '@'.$name.$suffix.': "'.(string) $value.'";';
    }
}
