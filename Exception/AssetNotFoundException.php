<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Exception;

/**
 * AssetNotFoundException for the Require asset.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetNotFoundException extends InvalidArgumentException
{
    public function __construct($asset, $code = 0, \Exception $previous = null)
    {
        $message = sprintf('The asset "%s" is not found', $asset);

        parent::__construct($message, $code, $previous);
    }
}
