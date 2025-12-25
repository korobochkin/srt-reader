<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Ast;

class SrtBlockNode
{
    private readonly string $state;
    private readonly array $children;
    private readonly int $offset;

    public function __construct(
        string $state,
        array $children,
        int $offset,
    ) {
        $this->state = $state;
        $this->children = $children;
        $this->offset = $offset;
    }

    public function getNumber(): int
    {
        return (int) $this->children[0]->getValue();
    }

    public function getStartTime(): string
    {
        return $this->children[1]->getValue();
    }

    public function getEndTime(): string
    {
        return $this->children[2]->getValue();
    }

    public function getStartTimeMs(): int
    {
        return $this->parseTimecode($this->getStartTime());
    }

    public function getEndTimeMs(): int
    {
        return $this->parseTimecode($this->getEndTime());
    }

    /**
     * @return string[]
     */
    public function getLines(): array
    {
        $lines = [];
        // Text tokens start at index 3
        for ($i = 3; $i < \count($this->children); $i++) {
            $lines[] = $this->children[$i]->getValue();
        }
        return $lines;
    }

    public function getText(): string
    {
        return implode("\n", $this->getLines());
    }

    /**
     * Convert "00:01:23,551" to milliseconds
     */
    private function parseTimecode(string $time): int
    {
        [$hms, $ms] = explode(',', $time);
        [$hours, $minutes, $seconds] = explode(':', $hms);

        return ((int) $hours * 3600000)
            + ((int) $minutes * 60000)
            + ((int) $seconds * 1000)
            + (int) $ms;
    }
}
