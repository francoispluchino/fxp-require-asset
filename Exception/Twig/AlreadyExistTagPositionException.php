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

/**
 * AlreadyExistTagPositionException for the already exist the twig function of tag position.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AlreadyExistTagPositionException extends RuntimeException
{
    /**
     * Constructor.
     *
     * @param string      $category The template tag category
     * @param string      $type     The template tag type
     * @param string|null $position The position in template
     * @param int         $lineno   The template line where the error occurred
     * @param string|null $filename The template file name where the error occurred
     * @param \Exception  $previous The previous exception used for the exception chaining
     */
    public function __construct($category, $type, $position = null, $lineno = -1, $filename = null, \Exception $previous = null)
    {
        $functionName = $category . ucfirst($type) . 'sPosition';
        $positionName = empty($position) ? '' : '"' . $position . '"';
        $message = sprintf('The twig function "%s(%s)" is already defined', $functionName, $positionName);

        parent::__construct($message, $lineno, $filename, $previous);
    }
}
