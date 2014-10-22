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

use Fxp\Component\RequireAsset\Exception\BadMethodCallException;

/**
 * Interface of config pattern manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface PatternManagerInterface
{
    /**
     * Check if the config of default pattern exist.
     *
     * @param string $pattern
     *
     * @return bool
     */
    public function hasDefaultPattern($pattern);

    /**
     * Adds the config of default pattern.
     *
     * @param string $pattern The pattern
     *
     * @return self
     *
     * @throws BadMethodCallException When the manager is resolved
     */
    public function addDefaultPattern($pattern);

    /**
     * Adds the configs of default pattern.
     *
     * @param array $patterns The list of pattern
     *
     * @return self
     *
     * @throws BadMethodCallException When the manager is resolved
     */
    public function addDefaultPatterns(array $patterns);

    /**
     * Removes the config of default pattern.
     *
     * @param string $pattern
     *
     * @return self
     *
     * @throws BadMethodCallException When the manager is resolved
     */
    public function removeDefaultPattern($pattern);

    /**
     * Gets the list of config of default pattern.
     *
     * @return array
     */
    public function getDefaultPatterns();
}
