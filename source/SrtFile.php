<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader;

class SrtFile implements \IteratorAggregate
{
    private string $filename;

    private int $test;

    private string $permissions;

    /**
     * @var resource
     */
    private $resource;

    private int $statisticsLinesRead = 0;

    public function __construct(string $filename, string $permissions = 'r')
    {
        $this->filename = $filename;
        $this->permissions = $permissions;
        $this->open();
    }

    protected function open(): void
    {
        $this->resource = fopen($this->filename, $this->permissions);
        if ($this->resource === false) {
            throw new \RuntimeException(\sprintf('Failed to open file "%s" with permissions "%s"', $this->filename, $this->permissions));
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    protected function close(): void
    {
        if (\is_resource($this->resource)) {
            fclose($this->resource);
        }
    }

    public function getIterator(): \Traversable
    {
        while (feof($this->resource) === false) {
            yield $this->statisticsLinesRead++ => fgets($this->resource);
        }
    }
}
