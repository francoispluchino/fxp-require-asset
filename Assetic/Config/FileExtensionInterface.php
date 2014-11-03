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

/**
 * Interface of config file extension.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface FileExtensionInterface extends ConfigInterface
{
    /**
     * Gets the assetic formulae options.
     *
     * @return array The assetic formulae options
     */
    public function getOptions();

    /**
     * Gets the assetic formulae filters.
     *
     * @return array The assetic formulae filters
     */
    public function getFilters();

    /**
     * Gets the output extensions.
     *
     * @return string
     */
    public function getOutputExtension();

    /**
     * Check if is debug mode.
     *
     * @return bool
     */
    public function isDebug();

    /**
     * Check if this extension is exclude or not.
     *
     * @return bool
     */
    public function isExclude();
}
