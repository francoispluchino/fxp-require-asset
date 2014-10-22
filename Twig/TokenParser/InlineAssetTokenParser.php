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

use Fxp\Component\RequireAsset\Twig\Node\InlineAssetReference;

/**
 * Token Parser for the 'asset' tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class InlineAssetTokenParser extends \Twig_TokenParser
{
    /**
     * @var string
     */
    protected $type;

    protected $defaultAttributes = array('keep_tag' => false);

    /**
     * Constructor.
     *
     * @param string $type The asset type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Parses a token and returns a node.
     *
     * @param \Twig_Token $token A Twig_Token instance
     *
     * @return \Twig_NodeInterface A Twig_NodeInterface instance
     *
     * @throws \Twig_Error_Syntax When attribute name is not a string or constant
     * @throws \Twig_Error_Syntax When attribute does not exist
     * @throws \Twig_Error_Syntax When attribute is not followed by "=" operator
     * @throws \Twig_Error_Syntax When the value name is not a string or constant
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $options = $this->getTagOptions();

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $name = uniqid($this->type);
        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);

        if (!$options['keep_tag']) {
            $this->removeTag($body, $lineno);
        }

        $body = new \Twig_Node_Block($name, $body, $lineno);

        $this->parser->setBlock($name, $body);
        $this->parser->pushLocalScope();
        $this->parser->pushBlockStack($name);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $this->parser->popBlockStack();
        $this->parser->popLocalScope();

        return new InlineAssetReference($name, $this->type, $lineno);
    }

    /**
     * Decide block end.
     *
     * @param \Twig_Token $token
     *
     * @return boolean
     */
    public function decideBlockEnd(\Twig_Token $token)
    {
        return $token->test('end' . $this->getTag());
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'inline_' . $this->type;
    }

    /**
     * Gets the options of the twig tag.
     *
     * @return array The tag options
     *
     * @throws \Twig_Error_Syntax When the attribute does not exist
     * @throws \Twig_Error_Syntax When the attribute is not followed by "=" operator
     */
    protected function getTagOptions()
    {
        $stream = $this->parser->getStream();
        $options = $this->defaultAttributes;

        if (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            do {
                $this->validateCurrentToken($stream, 'value');
                $attr = $stream->getCurrent()->getValue();
                $stream->next();

                if (!in_array($attr, array_keys($options))) {
                    throw new \Twig_Error_Syntax(sprintf('The attribute "%s" does not exist. Only attributes "%s" exists', $attr, implode('", ', array_keys($options))), $stream->getCurrent()->getLine(), $stream->getFilename());
                }

                if (!$stream->test(\Twig_Token::OPERATOR_TYPE, '=')) {
                    throw new \Twig_Error_Syntax("The attribute must be followed by '=' operator", $stream->getCurrent()->getLine(), $stream->getFilename());
                }

                $stream->next();
                $this->validateCurrentToken($stream, 'value');

                $options[$attr] = $this->parser->getExpressionParser()->parseExpression()->getAttribute('value');

            } while (!$stream->test(\Twig_Token::BLOCK_END_TYPE));
        }

        return $options;
    }

    /**
     * Removes tag.
     *
     * @param \Twig_Node $body
     * @param int        $lineno
     *
     * @return \Twig_Node
     */
    protected function removeTag(\Twig_Node $body, $lineno)
    {
        if (0 === count($body)) {
            $body = new \Twig_Node(array($body), array(), $lineno);
        }

        $this->removeTagContent($body, 0, '/(|\ \\t|\\n|\\n\ \\t)<[a-zA-Z\=\'\"\ \/]+>(\\n|\\r)/');
        $this->removeTagContent($body, count($body) - 1, '/(|\ \\t|\\n|\\n\ \\t|\\n)<\/[a-zA-Z]+>/');

        return $body;
    }

    /**
     * Removes html tag defined by pattern.
     *
     * @param \Twig_Node $body
     * @param int        $position
     * @param string     $pattern
     */
    protected function removeTagContent(\Twig_Node $body, $position, $pattern)
    {
        if ($body->getNode($position) instanceof \Twig_Node_Text) {
            $positionBody = $body->getNode($position)->getAttribute('data');
            $positionBody = preg_replace($pattern, '', $positionBody);

            $body->getNode($position)->setAttribute('data', $positionBody);
        }
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
}
