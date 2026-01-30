<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Tests\Integration\Abstracts\TestCases;

use Korobochkin\SrtReader\SrtGrammar;
use Korobochkin\SrtReader\SrtParser;
use PHPUnit\Framework\TestCase;

abstract class AbstractSrtParserTest extends TestCase
{
    protected SrtParser $parser;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new SrtParser(SrtGrammar::getGrammar());
    }
}
