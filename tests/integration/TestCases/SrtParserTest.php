<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Tests\Integration\TestCases;

use Korobochkin\SrtReader\Ast\SrtDocumentNode;
use Korobochkin\SrtReader\Tests\Integration\Abstracts\TestCases\AbstractSrtParserTest;
use Korobochkin\SrtReader\Tests\Integration\DataProviders\SrtDataInvalidProvider;
use Korobochkin\SrtReader\Tests\Integration\DataProviders\SrtDataValidProvider;
use PHPUnit\Framework\Attributes;

final class SrtParserTest extends AbstractSrtParserTest
{
    /**
     * @param string $content
     * @param SrtDocumentNode $expected
     * @return void
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \RuntimeException
     */
    #[Attributes\DataProviderExternal(SrtDataValidProvider::class, 'getValid')]
    public function testValidParse(string $content, SrtDocumentNode $expected): void
    {
        static::assertEquals($expected, $this->parser->parse($content));
    }

    /**
     * @param string $content
     * @param array{0: class-string<\Throwable>, 1: non-empty-string, 2: int} $expected
     * @return void
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \RuntimeException
     */
    #[Attributes\DataProviderExternal(SrtDataInvalidProvider::class, 'getInvalid')]
    public function testInvalidParse(string $content, array $expected): void
    {
        self::expectException($expected[0]);
        self::expectExceptionMessageMatches('/^' . preg_quote($expected[1], '/') . '/');
        self::expectExceptionCode($expected[2]);
        $this->parser->parse($content);
    }
}
