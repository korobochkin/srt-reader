<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader;

class SrtBlock
{
    private int $number;

    private int $startTime;

    private int $endTime;

    private string $text;

    public function __construct(int $number, int $startTime, int $endTime, string $text)
    {
        $this->number = $number;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->text = $text;
    }

    public function getNumber(): int
    {
        return $this->number;
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
}
