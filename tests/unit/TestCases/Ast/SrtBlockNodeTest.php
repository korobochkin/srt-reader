<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Tests\Unit\TestCases\Ast;

use Korobochkin\SrtReader\Ast\SrtBlockNode;
use Korobochkin\SrtReader\Tests\Unit\DataProviders\Ast\SrtBlockNodeDataProvider;
use Phplrt\Contracts\Ast\NodeInterface;
use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\TestCase;

#[Attributes\CoversClass(SrtBlockNode::class)]
final class SrtBlockNodeTest extends TestCase
{
    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testImplementsNodeInterface(): void
    {
        $node = new SrtBlockNode(1, 0, 1000, 'test');

        self::assertInstanceOf(NodeInterface::class, $node);
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testGetIndex(): void
    {
        $node = new SrtBlockNode(42, 0, 1000, 'test');

        self::assertSame(42, $node->getIndex());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testGetStartTime(): void
    {
        $node = new SrtBlockNode(1, 5000, 10000, 'test');

        self::assertSame(5000, $node->getStartTime());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testGetEndTime(): void
    {
        $node = new SrtBlockNode(1, 5000, 10000, 'test');

        self::assertSame(10000, $node->getEndTime());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testGetText(): void
    {
        $node = new SrtBlockNode(1, 0, 1000, 'Hello, world!');

        self::assertSame('Hello, world!', $node->getText());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testGetTextWithMultipleLines(): void
    {
        $text = "First line\nSecond line\nThird line";
        $node = new SrtBlockNode(1, 0, 1000, $text);

        self::assertSame($text, $node->getText());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testGetTextWithEmptyString(): void
    {
        $node = new SrtBlockNode(1, 0, 1000, '');

        self::assertSame('', $node->getText());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testGetIteratorReturnsEmptyIterator(): void
    {
        $node = new SrtBlockNode(1, 0, 1000, 'test');
        $iterator = $node->getIterator();

        self::assertInstanceOf(\EmptyIterator::class, $iterator);
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testGetIteratorIsTraversable(): void
    {
        $node = new SrtBlockNode(1, 0, 1000, 'test');

        self::assertInstanceOf(\Traversable::class, $node->getIterator());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testGetIteratorYieldsNoElements(): void
    {
        $node = new SrtBlockNode(1, 0, 1000, 'test');

        self::assertSame(0, iterator_count($node->getIterator()));
    }


    /**
     * @param int $index
     * @param int $startTime
     * @param int $endTime
     * @param string $text
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    #[Attributes\DataProviderExternal(SrtBlockNodeDataProvider::class, 'constructorDataProvider')]
    public function testConstructorStoresAllValues(
        int $index,
        int $startTime,
        int $endTime,
        string $text,
    ): void {
        $node = new SrtBlockNode($index, $startTime, $endTime, $text);

        self::assertSame($index, $node->getIndex());
        self::assertSame($startTime, $node->getStartTime());
        self::assertSame($endTime, $node->getEndTime());
        self::assertSame($text, $node->getText());
    }
}
