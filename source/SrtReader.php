<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader;

class SrtReader implements \IteratorAggregate
{
    private SrtFile $file;

    public function __construct(SrtFile $file)
    {
        $this->file = $file;
    }

    public function getIterator(): \Traversable
    {
        $buffer = [];

        foreach ($this->file as $line) {
            $line = trim($line);

            // Blank line = end of block
            if ($line === '') {
                if (\count($buffer) > 0) {
                    yield $this->parseBlock($buffer);
                    $buffer = [];
                }
                continue;
            }

            $buffer[] = $line;
        }

        // Handle last block if file doesn't end with blank line
        if (\count($buffer) > 0) {
            yield $this->parseBlock($buffer);
        }
    }

    private function parseBlock(array $lines): SrtBlock
    {
        // Line 0: block number
        $number = (int)$lines[0];

        // Line 1: timecode "00:01:23,551 --> 00:01:25,295"
        [$startTime, $endTime] = $this->parseTimecode($lines[1]);

        // Line 2+: text (join multiple lines)
        $text = implode("\n", \array_slice($lines, 2));

        return new SrtBlock($number, $startTime, $endTime, $text);
    }

    /**
     * Parse timecode string into milliseconds
     * Format: "00:01:23,551 --> 00:01:25,295"
     *
     * @return array{0: int, 1: int} [startTime, endTime] in milliseconds
     */
    private function parseTimecode(string $timecode): array
    {
        $parts = explode(' --> ', $timecode);

        return [
            $this->timeToMilliseconds($parts[0]),
            $this->timeToMilliseconds($parts[1]),
        ];
    }

    /**
     * Convert "00:01:23,551" to milliseconds
     */
    private function timeToMilliseconds(string $time): int
    {
        // Split "00:01:23,551" into ["00:01:23", "551"]
        [$hms, $ms] = explode(',', $time);

        // Split "00:01:23" into [0, 1, 23]
        [$hours, $minutes, $seconds] = explode(':', $hms);

        return ((int)$hours * 3600000)
            + ((int)$minutes * 60000)
            + ((int)$seconds * 1000)
            + (int)$ms;
    }
}
