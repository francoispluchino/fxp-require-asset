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
 * InvalidTwigConfigurationException for the Require asset.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InvalidTwigConfigurationException extends InvalidConfigurationException implements TwigExceptionInterface
{
    /**
     * Constructor.
     *
     * @param string      $tagType   The type of twig tag
     * @param string      $assetType The asset type
     * @param string|null $position  The position
     * @param int         $code      The Exception code
     * @param \Exception  $previous  The previous exception used for the exception chaining
     */
    public function __construct($tagType, $assetType, $position = null, $code = 0, \Exception $previous = null)
    {
        $functionName = $tagType . ucfirst($assetType) . 'sPosition';
        $tagName = $tagType . '_' . $assetType;
        $positionName = empty($position) ? '' : '"' . $position . '"';
        $message = sprintf('The twig function "%s(%s)" must be defined in the template, because it is required by a twig tag "%s" (%s)', $functionName, $positionName, $tagName, $positionName);

        parent::__construct($message, $code, $previous);
    }
}
