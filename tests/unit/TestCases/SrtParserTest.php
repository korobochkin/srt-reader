<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Tests\Unit\TestCases;

use Korobochkin\SrtReader\Ast\SrtDocumentNode;
use Korobochkin\SrtReader\SrtGrammar;
use Korobochkin\SrtReader\SrtParser;
use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\TestCase;

#[Attributes\CoversClass(SrtParser::class)]
final class SrtParserTest extends TestCase
{
    private SrtParser $parser;

    #[\Override]
    protected function setUp(): void
    {
        $this->parser = new SrtParser(SrtGrammar::getGrammar());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \RuntimeException
     */
    public function testParseReturnsDocumentNode(): void
    {
        $srt = "1\n00:00:00,000 --> 00:00:01,000\nHello world\n";

        $result = $this->parser->parse($srt);

        self::assertInstanceOf(SrtDocumentNode::class, $result);
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \RuntimeException
     */
    public function testParseEmptyStringReturnsEmptyDocument(): void
    {
        $result = $this->parser->parse('');

        self::assertSame(0, $result->count());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \RuntimeException
     */
    public function testParseSingleBlock(): void
    {
        $srt = "1\n00:00:00,000 --> 00:00:01,000\nHello world\n";

        $result = $this->parser->parse($srt);

        self::assertSame(1, $result->count());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \RuntimeException
     */
    public function testParseMultipleBlocks(): void
    {
        $srt = "1\n00:00:00,000 --> 00:00:01,000\nFirst\n\n2\n00:00:02,000 --> 00:00:03,000\nSecond\n";

        $result = $this->parser->parse($srt);

        self::assertSame(2, $result->count());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \RuntimeException
     */
    public function testParsedBlockHasCorrectIndex(): void
    {
        $srt = "42\n00:00:00,000 --> 00:00:01,000\nTest\n";

        $result = $this->parser->parse($srt);

        $blocks = iterator_to_array($result);
        self::assertSame(42, $blocks[0]->getIndex());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \RuntimeException
     */
    public function testParsedBlockHasCorrectStartTime(): void
    {
        $srt = "1\n01:23:45,678 --> 00:00:01,000\nTest\n";

        $result = $this->parser->parse($srt);

        $blocks = iterator_to_array($result);
        // 1*3600000 + 23*60000 + 45*1000 + 678 = 5025678
        self::assertSame(5025678, $blocks[0]->getStartTime());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \RuntimeException
     */
    public function testParsedBlockHasCorrectEndTime(): void
    {
        $srt = "1\n00:00:00,000 --> 02:34:56,789\nTest\n";

        $result = $this->parser->parse($srt);

        $blocks = iterator_to_array($result);
        // 2*3600000 + 34*60000 + 56*1000 + 789 = 9296789
        self::assertSame(9296789, $blocks[0]->getEndTime());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \RuntimeException
     */
    public function testParsedBlockHasCorrectText(): void
    {
        $srt = "1\n00:00:00,000 --> 00:00:01,000\nHello world\n";

        $result = $this->parser->parse($srt);

        $blocks = iterator_to_array($result);
        self::assertSame('Hello world', $blocks[0]->getText());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \RuntimeException
     */
    public function testParseFromStreamResource(): void
    {
        try {
            $srt = "1\n00:00:00,000 --> 00:00:01,000\nFrom stream\n";
            $stream = fopen('php://memory', 'r+');
            self::assertIsResource($stream);
            fwrite($stream, $srt);
            rewind($stream);

            $result = $this->parser->parse($stream);

            self::assertSame(1, $result->count());
            $blocks = iterator_to_array($result);
            self::assertSame('From stream', $blocks[0]->getText());
        } finally {
            /**
             * @psalm-suppress RedundantCondition
             */
            if (isset($stream) && \is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \RuntimeException
     */
    public function testParseFromEmptyStream(): void
    {
        try {
            $stream = fopen('php://memory', 'r+');
            self::assertIsResource($stream);

            $result = $this->parser->parse($stream);

            self::assertSame(0, $result->count());
        } finally {
            /**
             * @psalm-suppress RedundantConditionGivenDocblockType
             */
            if (isset($stream) && \is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \RuntimeException
     */
    public function testParseWithWindowsLineEndings(): void
    {
        $srt = "1\r\n00:00:00,000 --> 00:00:01,000\r\nWindows line endings\r\n";

        $result = $this->parser->parse($srt);

        self::assertSame(1, $result->count());
        $blocks = iterator_to_array($result);
        self::assertSame('Windows line endings', $blocks[0]->getText());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \RuntimeException
     */
    public function testParseMultilineText(): void
    {
        $srt = "1\n00:00:00,000 --> 00:00:01,000\nFirst line\nSecond line\n";

        $result = $this->parser->parse($srt);

        $blocks = iterator_to_array($result);
        self::assertSame('First line Second line', $blocks[0]->getText());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \RuntimeException
     */
    public function testParseWithPeriodTimeSeparator(): void
    {
        $srt = "1\n00:00:01.500 --> 00:00:02.750\nPeriod separator\n";

        $result = $this->parser->parse($srt);

        $blocks = iterator_to_array($result);
        self::assertSame(1500, $blocks[0]->getStartTime());
        self::assertSame(2750, $blocks[0]->getEndTime());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \RuntimeException
     */
    public function testParseWithUnicodeText(): void
    {
        $srt = "1\n00:00:00,000 --> 00:00:01,000\n日本語テキスト\n";

        $result = $this->parser->parse($srt);

        $blocks = iterator_to_array($result);
        self::assertSame('日本語テキスト', $blocks[0]->getText());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     * @throws \RuntimeException
     */
    public function testParseWithHtmlTags(): void
    {
        $srt = "1\n00:00:00,000 --> 00:00:01,000\n<i>Italic text</i>\n";

        $result = $this->parser->parse($srt);

        $blocks = iterator_to_array($result);
        self::assertSame('<i>Italic text</i>', $blocks[0]->getText());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     * @throws \RuntimeException
     * @throws \Phplrt\Contracts\Parser\ParserExceptionInterface
     * @throws \Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface
     * @throws \Phplrt\Contracts\Source\SourceExceptionInterface
     */
    public function testParseThrowsExceptionForInvalidSrt(): void
    {
        self::expectException(\Phplrt\Contracts\Parser\ParserExceptionInterface::class);

        $this->parser->parse("invalid srt content without proper format");
    }
}
