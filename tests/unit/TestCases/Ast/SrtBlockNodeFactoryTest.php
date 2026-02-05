<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Tests\Unit\TestCases\Ast;

use Korobochkin\SrtReader\Ast\SrtBlockNode;
use Korobochkin\SrtReader\Ast\SrtBlockNodeFactory;
use Korobochkin\SrtReader\Tests\Unit\Utilities\TimeCompositeFactory;
use Phplrt\Lexer\Token\Token;
use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\TestCase;

#[Attributes\CoversClass(SrtBlockNodeFactory::class)]
final class SrtBlockNodeFactoryTest extends TestCase
{
    /**
     * @return void
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     */
    public function testCreateReturnsBlockNode(): void
    {
        $children = array(
            new Token('T_INDEX', '1', 0),
            TimeCompositeFactory::createTimeComposite(0, 0, 1, 0),
            TimeCompositeFactory::createTimeComposite(0, 0, 2, 0),
            new Token('T_TEXT', 'Hello world', 0),
        );

        $result = SrtBlockNodeFactory::create($children);

        self::assertInstanceOf(SrtBlockNode::class, $result);
    }

    /**
     * @param positive-int $index
     * @return void
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    #[Attributes\DataProvider('indexDataProvider')]
    public function testCreateParsesIndexCorrectly(int $index): void
    {
        $children = array(
            new Token('T_INDEX', (string) $index, 0),
            TimeCompositeFactory::createTimeComposite(0, 0, 0, 0),
            TimeCompositeFactory::createTimeComposite(0, 0, 0, 0),
            new Token('T_TEXT', 'Hello world', 0),
        );

        $result = SrtBlockNodeFactory::create($children);

        self::assertSame($index, $result->getIndex());
    }

    /**
     * @return iterable<string, array{positive-int}>
     */
    public static function indexDataProvider(): iterable
    {
        yield 'first subtitle' => array(1);
        yield 'typical index' => array(42);
        yield 'three digits' => array(100);
        yield 'large index' => array(9999);
        yield 'very large index' => array(\PHP_INT_MAX);
    }

    /**
     * @return void
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCreateParsesStartTimeCorrectly(): void
    {
        $children = array(
            new Token('T_INDEX', '1', 0),
            TimeCompositeFactory::createTimeComposite(1, 23, 45, 678),
            TimeCompositeFactory::createTimeComposite(0, 0, 0, 0),
            new Token('T_TEXT', 'Hello world', 0),
        );

        $result = SrtBlockNodeFactory::create($children);

        // 1*3600000 + 23*60000 + 45*1000 + 678 = 3600000 + 1380000 + 45000 + 678 = 5025678
        self::assertSame(5025678, $result->getStartTime());
    }

    /**
     * @return void
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCreateParsesEndTimeCorrectly(): void
    {
        $children = array(
            new Token('T_INDEX', '1', 0),
            TimeCompositeFactory::createTimeComposite(0, 0, 0, 0),
            TimeCompositeFactory::createTimeComposite(2, 34, 56, 789),
            new Token('T_TEXT', 'Hello world', 0),
        );

        $result = SrtBlockNodeFactory::create($children);

        // 2*3600000 + 34*60000 + 56*1000 + 789 = 7200000 + 2040000 + 56000 + 789 = 9296789
        self::assertSame(9296789, $result->getEndTime());
    }

    /**
     * @return void
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCreateParsesSingleTextToken(): void
    {
        $children = array(
            new Token('T_INDEX', '1', 0),
            TimeCompositeFactory::createTimeComposite(0, 0, 0, 0),
            TimeCompositeFactory::createTimeComposite(0, 0, 0, 0),
            new Token('T_TEXT', 'Hello world', 0),
        );

        $result = SrtBlockNodeFactory::create($children);

        self::assertSame('Hello world', $result->getText());
    }

    /**
     * @return void
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCreateConcatenatesMultipleTextTokens(): void
    {
        $children = array(
            new Token('T_INDEX', '1', 0),
            TimeCompositeFactory::createTimeComposite(0, 0, 0, 0),
            TimeCompositeFactory::createTimeComposite(0, 0, 0, 0),
            new Token('T_TEXT', 'First line', 0),
            new Token('T_TEXT', 'Second line', 0),
            new Token('T_TEXT', 'Third line', 0),
        );

        $result = SrtBlockNodeFactory::create($children);

        self::assertSame('First line Second line Third line', $result->getText());
    }

    /**
     * @return void
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCreateTrimsTextTokenValues(): void
    {
        $children = array(
            new Token('T_INDEX', '1', 0),
            TimeCompositeFactory::createTimeComposite(0, 0, 0, 0),
            TimeCompositeFactory::createTimeComposite(0, 0, 0, 0),
            new Token('T_TEXT', '  Hello  ', 0),
            new Token('T_TEXT', '  World  ', 0),
        );

        $result = SrtBlockNodeFactory::create($children);

        self::assertSame('Hello World', $result->getText());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \InvalidArgumentException
     */
    public function testCreateThrowsExceptionForEmptyArray(): void
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid $children structure: expected at least 4 elements but got 0');

        /** @psalm-suppress InvalidArgument Intentionally testing invalid input */
        SrtBlockNodeFactory::create(array());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \InvalidArgumentException
     */
    public function testCreateThrowsExceptionForTooFewElements(): void
    {
        $children = array(
            new Token('T_INDEX', '1', 0),
            TimeCompositeFactory::createTimeComposite(0, 0, 0, 0),
            TimeCompositeFactory::createTimeComposite(0, 0, 0, 0),
        );

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid $children structure: expected at least 4 elements but got 3');

        /** @psalm-suppress InvalidArgument Intentionally testing invalid input */
        SrtBlockNodeFactory::create($children);
    }

    /**
     * @return void
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCreateTimeCalculatesMillisecondsCorrectly(): void
    {
        $time = TimeCompositeFactory::createTimeComposite(1, 30, 45, 500);

        $result = SrtBlockNodeFactory::createTime($time);

        // 1*3600000 + 30*60000 + 45*1000 + 500 = 3600000 + 1800000 + 45000 + 500 = 5445500
        self::assertSame(5445500, $result);
    }

    /**
     * @return void
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCreateTimeWithZeroValues(): void
    {
        $time = TimeCompositeFactory::createTimeComposite(0, 0, 0, 0);

        $result = SrtBlockNodeFactory::createTime($time);

        self::assertSame(0, $result);
    }

    /**
     * @return void
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCreateTimeWithMaximumValues(): void
    {
        $time = TimeCompositeFactory::createTimeComposite(99, 59, 59, 999);

        $result = SrtBlockNodeFactory::createTime($time);

        // 99*3600000 + 59*60000 + 59*1000 + 999 = 356400000 + 3540000 + 59000 + 999 = 359999999
        self::assertSame(359999999, $result);
    }

    /**
     * @param int<0, max> $hours
     * @param int<0, max> $minutes
     * @param int<0, max> $seconds
     * @param int<0, max> $milliseconds
     * @param int<0, max> $expected
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    #[Attributes\DataProvider('createTimeDataProvider')]
    public function testCreateTimeWithVariousInputs(
        int $hours,
        int $minutes,
        int $seconds,
        int $milliseconds,
        int $expected,
    ): void {
        $time = TimeCompositeFactory::createTimeComposite($hours, $minutes, $seconds, $milliseconds);

        $result = SrtBlockNodeFactory::createTime($time);

        self::assertSame($expected, $result);
    }

    /**
     * @return iterable<string, array{int<0, max>, int<0, max>, int<0, max>, int<0, max>, int<0, max>}>
     */
    public static function createTimeDataProvider(): iterable
    {
        yield 'zero time' => array(0, 0, 0, 0, 0);
        yield 'one millisecond' => array(0, 0, 0, 1, 1);
        yield 'one second' => array(0, 0, 1, 0, 1000);
        yield 'one minute' => array(0, 1, 0, 0, 60000);
        yield 'one hour' => array(1, 0, 0, 0, 3600000);
        yield 'typical subtitle time' => array(0, 1, 30, 500, 90500);
        yield 'complex calculation' => array(1, 23, 45, 678, 5025678);
        yield 'max boundary' => array(99, 59, 59, 999, 359999999);
        yield 'hours over 24' => array(25, 30, 45, 123, 91845123);
    }
}
