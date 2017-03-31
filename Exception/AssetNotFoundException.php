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
    /**
     * @param string          $asset    The asset
     * @param string|null     $type     The asset type
     * @param int             $code     The exception code
     * @param \Exception|null $previous The previous exception
     */
    public function __construct($asset, $type = null, $code = 0, \Exception $previous = null)
    {
        $type = null === $type ? '' : ' '.$type;
        $message = sprintf('The%s asset "%s" is not found', $type, $asset);

        parent::__construct($message, $code, $previous);
    }
}
