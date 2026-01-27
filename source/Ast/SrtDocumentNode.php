<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Ast;

/**
 * @psalm-api
 * @implements \IteratorAggregate<int, SrtBlockNode>
 */
class SrtDocumentNode implements \IteratorAggregate, \Countable
{
    /** @var list<SrtBlockNode> */
    private readonly array $children;

    /**
     * @param list<SrtBlockNode> $children
     */
    public function __construct(
        array $children,
    ) {
        $this->children = $children;
    }

    /**
     * @return \ArrayIterator
     *
     * @psalm-return \ArrayIterator<int<0, max>, SrtBlockNode>
     */
    #[\Override]
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->children);
    }

    /**
     * @return int
     *
     * @psalm-return int<0, max>
     */
    #[\Override]
    public function count(): int
    {
        return \count($this->children);
    }
}
