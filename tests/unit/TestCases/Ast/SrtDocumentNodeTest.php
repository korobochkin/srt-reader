<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Tests\Unit\TestCases\Ast;

use Korobochkin\SrtReader\Ast\SrtBlockNode;
use Korobochkin\SrtReader\Ast\SrtDocumentNode;
use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\TestCase;

#[Attributes\CoversClass(SrtDocumentNode::class)]
final class SrtDocumentNodeTest extends TestCase
{
    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testImplementsIteratorAggregate(): void
    {
        $document = new SrtDocumentNode(array());

        self::assertInstanceOf(\IteratorAggregate::class, $document);
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testImplementsCountable(): void
    {
        $document = new SrtDocumentNode(array());

        self::assertInstanceOf(\Countable::class, $document);
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testGetIteratorReturnsArrayIterator(): void
    {
        $document = new SrtDocumentNode(array());

        self::assertInstanceOf(\ArrayIterator::class, $document->getIterator());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testGetIteratorIsTraversable(): void
    {
        $document = new SrtDocumentNode(array());

        self::assertInstanceOf(\Traversable::class, $document->getIterator());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCountReturnsZeroForEmptyDocument(): void
    {
        $document = new SrtDocumentNode(array());

        self::assertSame(0, $document->count());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\GeneratorNotSupportedException
     */
    public function testCountReturnsSameAsNativeCount(): void
    {
        $document = new SrtDocumentNode(array());

        self::assertCount(0, $document);
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCountReturnsOneForSingleChild(): void
    {
        $block = new SrtBlockNode(1, 0, 1000, 'Test');
        $document = new SrtDocumentNode(array($block));

        self::assertSame(1, $document->count());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCountReturnsCorrectNumberForMultipleChildren(): void
    {
        $children = array(
            new SrtBlockNode(1, 0, 1000, 'First'),
            new SrtBlockNode(2, 1000, 2000, 'Second'),
            new SrtBlockNode(3, 2000, 3000, 'Third'),
        );
        $document = new SrtDocumentNode($children);

        self::assertSame(3, $document->count());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testIteratorYieldsNoElementsForEmptyDocument(): void
    {
        $document = new SrtDocumentNode(array());

        self::assertSame(0, iterator_count($document->getIterator()));
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testIteratorYieldsAllChildren(): void
    {
        $children = array(
            new SrtBlockNode(1, 0, 1000, 'First'),
            new SrtBlockNode(2, 1000, 2000, 'Second'),
            new SrtBlockNode(3, 2000, 3000, 'Third'),
        );
        $document = new SrtDocumentNode($children);

        self::assertSame(3, iterator_count($document->getIterator()));
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testIteratorYieldsChildrenInOrder(): void
    {
        $children = array(
            new SrtBlockNode(1, 0, 1000, 'First'),
            new SrtBlockNode(2, 1000, 2000, 'Second'),
            new SrtBlockNode(3, 2000, 3000, 'Third'),
        );
        $document = new SrtDocumentNode($children);

        $iteratedChildren = iterator_to_array($document);

        self::assertSame($children, $iteratedChildren);
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testIteratorYieldsSrtBlockNodeInstances(): void
    {
        $children = array(
            new SrtBlockNode(1, 0, 1000, 'Test'),
        );
        $document = new SrtDocumentNode($children);

        foreach ($document as $block) {
            self::assertInstanceOf(SrtBlockNode::class, $block);
        }
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testDocumentCanBeIteratedMultipleTimes(): void
    {
        $children = array(
            new SrtBlockNode(1, 0, 1000, 'First'),
            new SrtBlockNode(2, 1000, 2000, 'Second'),
        );
        $document = new SrtDocumentNode($children);

        $firstIteration = array();
        foreach ($document as $block) {
            $firstIteration[] = $block;
        }

        $secondIteration = array();
        foreach ($document as $block) {
            $secondIteration[] = $block;
        }

        self::assertSame($firstIteration, $secondIteration);
    }

    /**
     * @param list<SrtBlockNode> $children
     * @param int<0, max> $expectedCount
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    #[Attributes\DataProvider('childrenCountDataProvider')]
    public function testCountWithVariousChildrenCounts(array $children, int $expectedCount): void
    {
        $document = new SrtDocumentNode($children);

        self::assertSame($expectedCount, $document->count());
    }

    /**
     * @return iterable<string, array{list<SrtBlockNode>, int<0, max>}>
     */
    public static function childrenCountDataProvider(): iterable
    {
        yield 'empty document' => array(
            array(),
            0,
        );

        yield 'single block' => array(
            array(new SrtBlockNode(1, 0, 1000, 'Single')),
            1,
        );

        yield 'two blocks' => array(
            array(
                new SrtBlockNode(1, 0, 1000, 'First'),
                new SrtBlockNode(2, 1000, 2000, 'Second'),
            ),
            2,
        );

        yield 'five blocks' => array(
            array(
                new SrtBlockNode(1, 0, 1000, 'One'),
                new SrtBlockNode(2, 1000, 2000, 'Two'),
                new SrtBlockNode(3, 2000, 3000, 'Three'),
                new SrtBlockNode(4, 3000, 4000, 'Four'),
                new SrtBlockNode(5, 4000, 5000, 'Five'),
            ),
            5,
        );
    }
}
