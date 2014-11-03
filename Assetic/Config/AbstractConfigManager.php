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
 * Abstract config manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractConfigManager
{
    /**
     * Add the configs in the manager.
     *
     * @param array  $configs The configs
     * @param string $method  The method
     *
     * @return self
     */
    protected function addConfig(array $configs, $method)
    {
        foreach ($configs as $key => $config) {
            if (is_array($config) && !isset($config['name'])) {
                $config['name'] = $key;
            }

            $this->$method($config);
        }

        return $this;
    }
}
