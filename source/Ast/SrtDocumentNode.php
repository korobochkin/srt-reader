<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Ast;

class SrtDocumentNode implements \IteratorAggregate, \Countable
{
    /** @var list<SrtBlockNode> */
    private readonly array $children;

    public function __construct(
        array $children,
    ) {
        $this->children = $children;
    }

    /**
     * @return \Traversable<int, SrtBlockNode>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->children);
    }

    public function count(): int
    {
        return \count($this->children);
    }
}
