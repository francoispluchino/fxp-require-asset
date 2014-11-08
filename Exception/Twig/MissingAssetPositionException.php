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

use Fxp\Component\RequireAsset\Twig\Asset\TwigAssetInterface;

/**
 * MissingAssetPositionException for the missing twig function of asset position in tamplate.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class MissingAssetPositionException extends RuntimeException
{
    /**
     * Constructor.
     *
     * @param TwigAssetInterface $asset    The twig asset instance
     * @param \Exception         $previous The previous exception
     */
    public function __construct(TwigAssetInterface $asset, \Exception $previous = null)
    {
        $functionName = $asset->getCategory() . ucfirst($asset->getType()) . 'sPosition';
        $tagName = $asset->getCategory() . '_' . $asset->getType();
        $positionName = empty($asset->getPosition()) ? 'without position' : '"' . $asset->getPosition() . '"';
        $message = sprintf('The twig function "%s(%s)" must be defined in the template, because it is required by a twig tag "%s" (%s)', $functionName, $positionName, $tagName, $positionName);

        parent::__construct($message, $asset->getLineno(), $asset->getFilename(), $previous);
    }
}
