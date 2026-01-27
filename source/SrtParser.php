<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader;

use Korobochkin\SrtReader\Ast\SrtDocumentNode;
use Phplrt\Contracts\Lexer\LexerInterface;
use Phplrt\Contracts\Parser\ParserInterface;
use Phplrt\Lexer\Lexer;
use Phplrt\Parser\Parser;
use Phplrt\Parser\ParserConfigsInterface;
use Phplrt\Source\Source;

/**
 * @psalm-api
 */
class SrtParser
{
    /** @psalm-suppress UnusedProperty */
    private LexerInterface $lexer;

    private ParserInterface $parser;

    /**
    * @param array{
    *     initial: array-key,
    *     tokens: array{
    *       default: array<non-empty-string, non-empty-string>
    *     },
    *     skip: list<non-empty-string>,
    *     grammar: array<array-key, \Phplrt\Parser\Grammar\RuleInterface>,
    *     reducers: array{
    *        Block: callable(\Phplrt\Parser\Context, array{0: \Phplrt\Lexer\Token\Token, 1: \Phplrt\Lexer\Token\Composite, 2: \Phplrt\Lexer\Token\Composite, 3: \Phplrt\Lexer\Token\Token, ...<int, \Phplrt\Lexer\Token\Token>}):\Korobochkin\SrtReader\Ast\SrtBlockNode,
    *        Document: callable(\Phplrt\Parser\Context, list<\Korobochkin\SrtReader\Ast\SrtBlockNode>):\Korobochkin\SrtReader\Ast\SrtDocumentNode
    *     },
    *     transitions?: array<array-key, mixed>
    * } $config
    */
    public function __construct(array $config)
    {
        \assert(isset($config['tokens']['default']), 'Missing "tokens.default" in config');
        \assert(isset($config['skip']), 'Missing "skip" in config');
        \assert(isset($config['grammar']), 'Missing "grammar" in config');
        \assert(isset($config['initial']), 'Missing "initial" in config');
        \assert(isset($config['reducers']), 'Missing "reducers" in config');

        $this->lexer = new Lexer(
            $config['tokens']['default'],
            $config['skip'],
        );
        $this->parser = new Parser(
            $this->lexer,
            $config['grammar'],
            array(
                ParserConfigsInterface::CONFIG_INITIAL_RULE => $config['initial'],
                ParserConfigsInterface::CONFIG_AST_BUILDER => $config['reducers'],
            )
        );
    }

    /**
     * @param string|resource $source The SRT content as a string, or a file resource
     * @return SrtDocumentNode
     * @throws \RuntimeException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     */
    public function parse($source): SrtDocumentNode
    {
        /**
         * Convert stream resources to strings to avoid an infinite loop bug in phplrt.
         * When parsing fails with a stream resource, phplrt's PositionFactory tries to
         * read from the stream to create error messages, but the stream position is at
         * EOF after lexing, causing an infinite loop in the fread() loop.
         *
         * @see \Phplrt\Position\PositionFactory::createFromOffset
         */
        if (\is_resource($source) && get_resource_type($source) === 'stream') {
            $source = stream_get_contents($source);
            if ($source === false) {
                throw new \RuntimeException('Failed to read stream content');
            }
        }

        $result = $this->parser->parse(Source::new($source));

        \assert($result instanceof SrtDocumentNode);

        return $result;
    }
}
