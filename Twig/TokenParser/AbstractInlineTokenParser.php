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

use Fxp\Component\RequireAsset\Tag\Config\InlineTagConfiguration;
use Fxp\Component\RequireAsset\Twig\Extension\AssetExtension;
use Fxp\Component\RequireAsset\Twig\Node\InlineTagReference;

/**
 * Abstract Token Parser for the 'inline_TYPE' tag.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractInlineTokenParser extends AbstractTokenParser
{
    /**
     * @var string
     */
    protected $extension;

    /**
     * Constructor.
     *
     * @param string|null $extension The class name of twig extension
     */
    public function __construct($extension = null)
    {
        $this->extension = null !== $extension ? $extension : AssetExtension::class;
    }

    /**
     * Parses a token and returns a node.
     *
     * @param \Twig_Token $token A Twig_Token instance
     *
     * @return \Twig_Node A Twig_Node instance
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
        $attributes = $this->getTagAttributes();
        $position = $this->getPosition($attributes);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $name = uniqid($this->getTag());
        $body = $this->parser->subparse([$this, 'decideBlockEnd'], true);

        if (!$attributes['keep_html_tag']) {
            $this->removeHtmlTag($body, $lineno);
        }

        $body = new \Twig_Node_Block($name, $body, $lineno);

        $this->parser->setBlock($name, $body);
        $this->parser->pushLocalScope();
        $this->parser->pushBlockStack($name);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $this->parser->popBlockStack();
        $this->parser->popLocalScope();

        return new InlineTagReference($this->extension, $name, $this->getTagClass(), $lineno, $position);
    }

    /**
     * Decide block end.
     *
     * @param \Twig_Token $token
     *
     * @return bool
     */
    public function decideBlockEnd(\Twig_Token $token)
    {
        return $token->test('end'.$this->getTag());
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeNodeConfig()
    {
        return InlineTagConfiguration::getNode();
    }

    /**
     * Removes tag.
     *
     * @param \Twig_Node $body
     * @param int        $lineno
     *
     * @return \Twig_Node
     */
    protected function removeHtmlTag(\Twig_Node $body, $lineno)
    {
        if (0 === \count($body)) {
            $body = new \Twig_Node([$body], [], $lineno);
        }

        $this->removeTagContent($body, 0, '/(|\ \\t|\\n|\\n\ \\t)<([a-zA-Z0-9]+)[a-zA-Z\=\'\"\ \/]+>(\\n?|\\r?)/');
        $this->removeTagContent($body, \count($body) - 1, '/(|\ \\t|\\n|\\n\ \\t|\\n)<\/[a-zA-Z]+>/');

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
}
