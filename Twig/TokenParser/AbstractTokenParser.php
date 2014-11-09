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

use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Definition\Processor;

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
        $attributes = array();
        $lineno = $stream->getCurrent()->getLine();
        $filename = $stream->getFilename();

        if (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            do {
                $this->validateCurrentToken($stream, 'attribute');
                $attr = $stream->getCurrent()->getValue();
                $stream->next();
                $this->validateAttribute($stream, $attr);
                $stream->next();
                $this->validateCurrentToken($stream, 'value');

                $attributes[$attr] = $this->parser->getExpressionParser()->parseExpression()->getAttribute('value');

            } while (!$stream->test(\Twig_Token::BLOCK_END_TYPE));
        }

        return $this->formatAttributes($attributes, $lineno, $filename);
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
     * @param \Twig_TokenStream $stream The token stream
     * @param string            $attr   The attribute name
     *
     * @throws \Twig_Error_Syntax When the attribute is not following by "="
     */
    protected function validateAttribute(\Twig_TokenStream $stream, $attr)
    {
        if (!$stream->test(\Twig_Token::OPERATOR_TYPE, '=')) {
            throw new \Twig_Error_Syntax(sprintf('The attribute "%s" must be followed by "=" operator', $attr), $stream->getCurrent()->getLine(), $stream->getFilename());
        }
    }

    /**
     * Validate and format all attributes.
     *
     * @param array  $attributes
     * @param int    $lineno
     * @param string $filename
     *
     * @return array The formatted attributes
     *
     * @throws \Twig_Error_Syntax When the attribute does not exist
     */
    protected function formatAttributes(array $attributes, $lineno, $filename)
    {
        try {
            $processor = new Processor();

            return $processor->process($this->getAttributeNodeConfig(), array($attributes));

        } catch (\Exception $e) {
            throw new \Twig_Error_Syntax($this->getFormattedMessageException($e), $lineno, $filename);
        }
    }

    /**
     * Get the formatted message excpetion of the attributes validation and formatting.
     *
     * @param \Exception $exception The exception
     *
     * @return string The exception message
     */
    protected function getFormattedMessageException(\Exception $exception)
    {
        if ($exception instanceof InvalidTypeException) {
            $attribute = $this->getExceptionAttribute($exception->getMessage());
            $attribute = substr($attribute, strrpos($attribute, '.') + 1);
            $message = sprintf('Invalid type for attribute "%s"', $attribute);
            $message .= substr($exception->getMessage(), strrpos($exception->getMessage(), '". ') + 1);

            return $message;
        }

        $attribute = $this->getExceptionAttribute($exception->getMessage());
        $message = sprintf('The attribute "%s" does not exist for the "%s" tag', $attribute, $this->getTag());
        $validNode = $this->getAttributeNodeConfig();

        if ($validNode instanceof ArrayNode) {
            $message .= sprintf('. Only attributes "%s" are available', implode('", ', array_keys($validNode->getChildren())));
        }

        return $message;
    }

    /**
     * Get the attribute name in the message of config exception.
     *
     * @param string $message The message exception
     *
     * @return string The attribute name
     */
    protected function getExceptionAttribute($message)
    {
        $message = substr($message, strpos($message, '"') + 1);

        return substr($message, 0, strpos($message, '"'));
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

    /**
     * @return NodeInterface
     */
    abstract protected function getAttributeNodeConfig();
}