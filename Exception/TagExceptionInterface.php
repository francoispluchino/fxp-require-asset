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

use Fxp\Component\RequireAsset\Tag\TagInterface;

/**
 * Base TagExceptionInterface for the template tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface TagExceptionInterface
{
    /**
     * @return TagInterface
     */
    public function getTag();
}
