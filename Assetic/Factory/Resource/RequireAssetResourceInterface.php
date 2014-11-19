<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic\Factory\Resource;

use Assetic\Factory\Resource\ResourceInterface;

/**
 * Interface of require asset resource.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface RequireAssetResourceInterface extends ResourceInterface
{
    /**
     * Get the config of assetic formulae.
     *
     * @return array
     */
    public function getFormulae();

    /**
     * Get the pretty name.
     *
     * @return string
     */
    public function getPrettyName();
}
