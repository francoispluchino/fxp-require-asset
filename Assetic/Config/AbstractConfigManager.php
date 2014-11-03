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

    /**
     * Do add action.
     *
     * @param string $class    The class name for do the action
     * @param string $property The property for save result
     * @param array  $args     The arguments
     *
     * @return self
     */
    protected function doAdd($class, $property, array $args)
    {
        $this->validate();

        $config = call_user_func_array($class . '::createByConfig', $args);
        $prop = &$this->$property;
        $prop[$config->getName()][] = $config;

        return $this;
    }

    /**
     * Validate the instance.
     *
     * @throws BadMethodCallException When the config manager is locked
     */
    abstract protected function validate();
}
