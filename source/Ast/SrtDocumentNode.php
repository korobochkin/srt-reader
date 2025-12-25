<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Ast;

class SrtDocumentNode implements \IteratorAggregate
{
    public readonly string $state;

    /** @var array<SrtBlockNode> */
    public readonly array $children;
    public readonly int $offset;

    public function __construct(
        string $state,
        array $children,
        int $offset,
    ) {
        $this->state = $state;
        $this->children = $children;
        $this->offset = $offset;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->children);
    }

    public function count(): int
    {
        return \count($this->children);
    }
}
