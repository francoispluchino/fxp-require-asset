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
 * Interface of base configuration.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface ConfigInterface
{
    /**
     * Gets the config name.
     *
     * @return string
     */
    public function getName();
}
