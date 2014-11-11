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
 * Base TagException for the template tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class TagException extends InvalidConfigurationException implements TagExceptionInterface
{
    /**
     * @var TagInterface
     */
    protected $tag;

    /**
     * Constructor.
     *
     * @param TagInterface $tag      The template tag
     * @param string       $message  The message exception
     * @param int          $code     The code exception
     * @param \Exception   $previous The previous exception
     */
    public function __construct(TagInterface $tag, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->tag = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return $this->tag;
    }
}
