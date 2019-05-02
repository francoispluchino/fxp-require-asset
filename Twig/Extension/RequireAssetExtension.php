<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\Extension;

use Fxp\Component\RequireAsset\Asset\RequireAssetManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * RequireAssetExtension extends Twig with global assets rendering capabilities.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetExtension extends AbstractExtension
{
    /**
     * @var null|RequireAssetManagerInterface
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param null|RequireAssetManagerInterface $manager The require asset manager
     */
    public function __construct(RequireAssetManagerInterface $manager = null)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('requireAsset', [$this, 'requireAsset']),
        ];
    }

    /**
     * Get the target path of the require asset.
     *
     * @param string      $asset The require asset name
     * @param null|string $type  The asset type
     *
     * @return string
     */
    public function requireAsset($asset, $type = null)
    {
        return null !== $this->manager && $this->manager->has($asset, $type)
            ? $this->manager->getPath($asset, $type)
            : $asset;
    }
}
