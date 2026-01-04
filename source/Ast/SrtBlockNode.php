<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Ast;

use Phplrt\Contracts\Ast\NodeInterface;

class SrtBlockNode implements NodeInterface
{
    private readonly int $index;
    private readonly int $startTime;
    private readonly int $endTime;
    private readonly string $text;

    public function __construct(
        int $index,
        int $startTime,
        int $endTime,
        string $text,
    ) {
        $this->index = $index;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->text = $text;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function getEndTime(): int
    {
        return $this->endTime;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getIterator(): \Traversable
    {
        return new \EmptyIterator();
    }
}
