<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Assetic\Factory\Loader;

use Assetic\Factory\Loader\FormulaLoaderInterface;
use Assetic\Factory\Resource\ResourceInterface;
use Fxp\Bundle\RequireAssetBundle\Assetic\AssetManagerInterface;
use Fxp\Bundle\RequireAssetBundle\Assetic\Factory\Resource\RequireAssetResource;
use Fxp\Bundle\RequireAssetBundle\Assetic\Util\Utils;

/**
 * Creates formulaes for font resources.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetLoader implements FormulaLoaderInterface
{
    /**
     * @var AssetManagerInterface
     */
    protected $assetManager;

    /**
     * Constructor.
     *
     * @param AssetManagerInterface $assetManager The asset maanger
     */
    public function __construct(AssetManagerInterface $assetManager)
    {
        $this->assetManager = $assetManager;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ResourceInterface $resource)
    {
        $formulae = array();

        if (!$resource instanceof RequireAssetResource) {
            return $formulae;
        }

        foreach ($this->assetManager->getAssets() as $asset) {
            $name = Utils::getFormulaeName($asset->getName());
            $formulae[$name] = array(
                // inputs
                array($asset->getSourcePath()),
                // filters
                $asset->getFilters(),
                // options
                array_merge($asset->getOptions(), array(
                    'output' => $asset->getOutputTarget(),
                    'debug'  => false,
                ))
            );
        }

        return $formulae;
    }
}
