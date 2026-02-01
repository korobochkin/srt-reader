<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Ast;

use Phplrt\Lexer\Token\Composite;
use Phplrt\Lexer\Token\Token;

/**
 * @psalm-api
 */
class SrtBlockNodeFactory
{
    /**
     * @param array{0: Token, 1: Composite, 2: Composite, 3: Token, ...<int, Token>} $children
     * @return SrtBlockNode
     * @throws \InvalidArgumentException
     */
    public static function create(array $children): SrtBlockNode
    {
        if (($childrenCounter = \count($children)) < 4) {
            throw new \InvalidArgumentException('Invalid $children structure: expected at least 4 elements but got ' . $childrenCounter);
        }
        return new SrtBlockNode(
            (int) $children[0]->getValue(),
            self::createTime($children[1]),
            self::createTime($children[2]),
            implode(
                ' ',
                array_map(
                    static fn(Token $token) => trim($token->getValue()),
                    \array_slice($children, 3),
                ),
            ),
        );
    }

    /**
     * @param Composite $time
     * @return int
     * @psalm-suppress PossiblyNullReference 4 elements guaranteed by grammar
     */
    public static function createTime(Composite $time): int
    {
        return
            ((int) $time[0]->getValue() * 3600000) // hours
            + ((int) $time[1]->getValue() * 60000) // minutes
            + ((int) $time[2]->getValue() * 1000) // seconds
            + (int) $time[3]->getValue(); // milliseconds
    }
}
