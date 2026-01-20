<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Ast;

use Phplrt\Contracts\Ast\NodeInterface;

/**
 * @psalm-api
 */
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

    /**
     * @psalm-api
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @psalm-api
     */
    public function getStartTime(): int
    {
        return $this->startTime;
    }

    /**
     * @psalm-api
     */
    public function getEndTime(): int
    {
        return $this->endTime;
    }

    /**
     * @psalm-api
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @psalm-api
     */
    public function getIterator(): \Traversable
    {
        return new \EmptyIterator();
    }
}
