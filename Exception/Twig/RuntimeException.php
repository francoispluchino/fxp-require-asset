<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Exception\Twig;

use Twig\Error\RuntimeError;

/**
 * Twig RuntimeException for the Require asset.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RuntimeException extends RuntimeError implements ExceptionInterface
{
}
