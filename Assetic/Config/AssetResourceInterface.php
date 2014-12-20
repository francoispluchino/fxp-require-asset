<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic\Config;

use Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResourceInterface;
use Fxp\Component\RequireAsset\Exception\InvalidConfigurationException;

/**
 * Interface of asset resource configuration (for asset manager).
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface AssetResourceInterface
{
    /**
     * Gets the assetic name.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the assetic pretty name.
     *
     * @return string
     */
    public function getPrettyName();

    /**
     * Gets the class name.
     *
     * @return string
     */
    public function getClassname();

    /**
     * Get the assetic loader name.
     *
     * @return string
     */
    public function getLoader();

    /**
     * Get the arguments of the class.
     *
     * @return array
     */
    public function getArguments();

    /**
     * Get the position of the asset name in arguments.
     *
     * @return int|null
     */
    public function getArgumentNamePosition();

    /**
     * Get the instance of the asset resource defined by the class.
     *
     * @return RequireAssetResourceInterface
     *
     * @throws InvalidConfigurationException When the new object is not an instance of "Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResourceInterface"
     */
    public function getNewInstance();
}
