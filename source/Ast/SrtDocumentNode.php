<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Ast;

class SrtDocumentNode implements \IteratorAggregate
{
    /** @var list<SrtBlockNode> */
    public readonly array $children;

    public function __construct(
        array $children,
    ) {
        $this->children = $children;
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
