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

use Fxp\Component\RequireAsset\Tag\TagInterface;
use Twig\Source;

/**
 * MissingTagPositionException for the missing tag position in tamplate.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class MissingTagPositionException extends RuntimeException
{
    /**
     * Constructor.
     *
     * @param TagInterface $asset    The template tag instance
     * @param \Exception   $previous The previous exception
     */
    public function __construct(TagInterface $asset, \Exception $previous = null)
    {
        $functionName = $asset->getCategory().ucfirst($asset->getType()).'sPosition';
        $tagName = $asset->getCategory().'_'.$asset->getType();
        $positionName = $asset->getPosition();
        $positionName = empty($positionName) ? 'without position' : '"'.$positionName.'"';
        $message = sprintf('The twig function "%s(%s)" must be defined in the template, because it is required by a twig tag "%s" (%s)', $functionName, $positionName, $tagName, $positionName);

        parent::__construct($message, $asset->getTemplateLine(), $asset->getTemplateName() ? new Source('', $asset->getTemplateName()) : null, $previous);
    }
}
