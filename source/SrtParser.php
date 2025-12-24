<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader;

use Phplrt\Compiler\Runtime\PrintableNodeBuilder;
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
            [
                ParserConfigsInterface::CONFIG_INITIAL_RULE => $config['initial'],
                ParserConfigsInterface::CONFIG_AST_BUILDER => new PrintableNodeBuilder(),
            ]
        );
    }

    /**
     * @param resource $source
     * @return iterable
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     */
    public function parse($source): iterable
    {
        return $this->parser->parse($source);
    }
}
