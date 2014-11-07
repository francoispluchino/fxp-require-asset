<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\TokenParser;

/**
 * Token Parser for the 'asset' tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractTokenParser extends \Twig_TokenParser
{
    /**
     * Gets the attributes of the twig tag.
     *
     * @return array The tag attributes
     *
     * @throws \Twig_Error_Syntax When the attribute does not exist
     * @throws \Twig_Error_Syntax When the attribute is not followed by "=" operator
     */
    protected function getTagAttributes()
    {
        $stream = $this->parser->getStream();
        $attributes = $this->getDefaultAttributes();

        if (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            do {
                $this->validateCurrentToken($stream, 'value');
                $attr = $stream->getCurrent()->getValue();
                $stream->next();
                $this->validateAttribute($stream, $attributes, $attr);
                $stream->next();
                $this->validateCurrentToken($stream, 'value');

                $attributes[$attr] = $this->parser->getExpressionParser()->parseExpression()->getAttribute('value');

            } while (!$stream->test(\Twig_Token::BLOCK_END_TYPE));
        }

        return $attributes;
    }

    /**
     * Validates the current token.
     *
     * @param \Twig_TokenStream $stream The token stream
     * @param string            $type   The token type name
     *
     * @throws \Twig_Error_Syntax When the token type is not valid
     */
    protected function validateCurrentToken(\Twig_TokenStream $stream, $type)
    {
        if (!$stream->test(\Twig_Token::NAME_TYPE)
            && !$stream->test(\Twig_Token::STRING_TYPE)) {
            throw new \Twig_Error_Syntax(sprintf('The %s name "%s" must be an STRING or CONSTANT', $type, $stream->getCurrent()->getValue()), $stream->getCurrent()->getLine(), $stream->getFilename());
        }
    }

    /**
     * Validate the current attribute.
     *
     * @param \Twig_TokenStream $stream     The token stream
     * @param array             $attributes The options
     * @param string            $attr       The name of attributes
     *
     * @throws \Twig_Error_Syntax When the attribute does not exist
     */
    protected function validateAttribute(\Twig_TokenStream $stream, array $attributes, $attr)
    {
        if (!in_array($attr, array_keys($attributes))) {
            throw new \Twig_Error_Syntax(sprintf('The attribute "%s" does not exist. Only attributes "%s" exists', $attr, implode('", ', array_keys($attributes))), $stream->getCurrent()->getLine(), $stream->getFilename());
        }

        if (!$stream->test(\Twig_Token::OPERATOR_TYPE, '=')) {
            throw new \Twig_Error_Syntax("The attribute must be followed by '=' operator", $stream->getCurrent()->getLine(), $stream->getFilename());
        }
    }

    /**
     * Get the default twig tag attributes.
     *
     * @return array
     */
    protected function getDefaultAttributes()
    {
        return array(
            'position' => null,
        );
    }

    /**
     * Get the position in template.
     * Remove the attribute "position" if it exists.
     *
     * @param array $attributes The attributes of twig tag.
     *
     * @return string|null The name position in the template
     */
    protected function getPosition(array &$attributes)
    {
        $position = isset($attributes['position']) ? $attributes['position'] : null;
        unset($attributes['position']);

        return $position;
    }

    /**
     * Get the class name of the twig asset config.
     *
     * @return string
     */
    abstract protected function getTwigAssetClass();
}
