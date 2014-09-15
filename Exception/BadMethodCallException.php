<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Exception;

/**
 * Base BadMethodCallException for the Require asset.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class BadMethodCallException extends \BadMethodCallException implements ExceptionInterface
{
}
