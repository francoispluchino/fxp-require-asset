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
 * Interface of config output manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface OutputManagerInterface
{
    /**
     * Check if the config of output pattern exist.
     *
     * @param string $pattern
     *
     * @return bool
     */
    public function hasOutputPattern($pattern);

    /**
     * Adds the config of output pattern.
     *
     * @param string $pattern       The pattern
     * @param string $outputPattern The output pattern
     *
     * @return self
     */
    public function addOutputPattern($pattern, $outputPattern);

    /**
     * Adds the configs of output pattern.
     *
     * @param array $patterns The list of pattern
     *
     * @return self
     */
    public function addOutputPatterns(array $patterns);

    /**
     * Removes the config of output pattern.
     *
     * @param string $pattern
     *
     * @return self
     */
    public function removeOutputPattern($pattern);

    /**
     * Gets the list of config of output pattern.
     *
     * @return array
     */
    public function getOutputPatterns();

    /**
     * Convert the output path.
     *
     * @param string $output The output path
     *
     * @return string The converted output path
     */
    public function convertOutput($output);
}
