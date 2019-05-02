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
     * @throws \Twig_Error_Syntax When the attribute does not exist
     * @throws \Twig_Error_Syntax When the attribute is not followed by "=" operator
     *
     * @return array The tag attributes
     */
    protected function getTagAttributes()
    {
        $stream = $this->parser->getStream();
        $attributes = [];
        $lineno = $stream->getCurrent()->getLine();
        $name = $stream->getSourceContext()->getName();

        if (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            do {
                $this->validateAttributeType($stream, 'name', ['NAME', 'STRING']);
                $attr = $stream->getCurrent()->getValue();
                $stream->next();
                $this->validateAttributeOperator($stream, $attr);
                $stream->next();
                $this->validateAttributeType($stream, 'value', ['NAME', 'STRING', 'NUMBER']);

                $attributes[$attr] = $this->parser->getExpressionParser()->parseExpression()->getAttribute('value');
            } while (!$stream->test(\Twig_Token::BLOCK_END_TYPE));
        }

        return $this->formatAttributes($attributes, $lineno, $name);
    }

    /**
     * Validate the current attribute type.
     *
     * @param \Twig_TokenStream $stream
     * @param string            $type
     * @param string[]          $allowed
     *
     * @throws \Twig_Error_Syntax When the attribute type is not allowed
     */
    protected function validateAttributeType(\Twig_TokenStream $stream, $type, array $allowed): void
    {
        $valid = false;

        foreach ($allowed as $aType) {
            if ($stream->test(\constant('\Twig_Token::'.$aType.'_TYPE'))) {
                $valid = true;

                break;
            }
        }

        if (!$valid) {
            $message = 'The attribute %s "%s" must be an %s';

            throw new \Twig_Error_Syntax(sprintf($message, $type, $stream->getCurrent()->getValue(), implode(', ', $allowed)), $stream->getCurrent()->getLine(), $stream->getSourceContext());
        }
    }

    /**
     * Validate the current attribute operator.
     *
     * @param \Twig_TokenStream $stream The token stream
     * @param string            $attr   The attribute name
     *
     * @throws \Twig_Error_Syntax When the attribute is not following by "="
     */
    protected function validateAttributeOperator(\Twig_TokenStream $stream, $attr): void
    {
        if (!$stream->test(\Twig_Token::OPERATOR_TYPE, '=')) {
            throw new \Twig_Error_Syntax(sprintf('The attribute "%s" must be followed by "=" operator', $attr), $stream->getCurrent()->getLine(), $stream->getSourceContext());
        }
    }

    /**
     * Validate and format all attributes.
     *
     * @param array  $attributes
     * @param int    $lineno
     * @param string $name
     *
     * @throws \Twig_Error_Syntax When the attribute does not exist
     *
     * @return array The formatted attributes
     */
    protected function formatAttributes(array $attributes, $lineno, $name)
    {
        try {
            $processor = new Processor();

            return $processor->process($this->getAttributeNodeConfig(), [$attributes]);
        } catch (\Exception $e) {
            throw new \Twig_Error_Syntax($this->getFormattedMessageException($e), $lineno, new \Twig_Source('', $name));
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
            $message .= sprintf('. Only attributes "%s" are available', implode('", "', array_keys($validNode->getChildren())));
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
     * @param array $attributes The attributes of twig tag
     *
     * @return null|string The name position in the template
     */
    protected function getPosition(array &$attributes)
    {
        $position = $attributes['position'] ?? null;
        unset($attributes['position']);

        return $position;
    }

    /**
     * Get the class name of the twig asset config.
     *
     * @return string
     */
    abstract protected function getTagClass();

    /**
     * @return NodeInterface
     */
    abstract protected function getAttributeNodeConfig();
}
