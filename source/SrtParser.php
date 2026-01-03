<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader;

use Phplrt\Contracts\Lexer\LexerInterface;
use Phplrt\Contracts\Parser\ParserInterface;
use Phplrt\Lexer\Lexer;
use Phplrt\Parser\Parser;
use Phplrt\Parser\ParserConfigsInterface;

class SrtParser
{
    private LexerInterface $lexer;

    private ParserInterface $parser;

    public function __construct(array $config)
    {
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
     * @return iterable
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     */
    public function parse($source): iterable
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
        }

        return $this->parser->parse($source);
    }
}
