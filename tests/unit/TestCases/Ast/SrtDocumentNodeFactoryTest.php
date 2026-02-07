<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Tests\Unit\TestCases\Ast;

use Korobochkin\SrtReader\Ast\SrtBlockNode;
use Korobochkin\SrtReader\Ast\SrtDocumentNode;
use Korobochkin\SrtReader\Ast\SrtDocumentNodeFactory;
use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\TestCase;

#[Attributes\CoversClass(SrtDocumentNodeFactory::class)]
final class SrtDocumentNodeFactoryTest extends TestCase
{
    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testCreateReturnsDocumentNode(): void
    {
        $result = SrtDocumentNodeFactory::create(array());

        self::assertInstanceOf(SrtDocumentNode::class, $result);
        self::assertSame(0, $result->count());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCreateWithSingleChildReturnsDocumentWithOneChild(): void
    {
        $children = array(
            new SrtBlockNode(1, 0, 1000, 'Hello world'),
        );

        $result = SrtDocumentNodeFactory::create($children);

        self::assertSame(1, $result->count());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCreateWithMultipleChildrenReturnsDocumentWithAllChildren(): void
    {
        $children = array(
            new SrtBlockNode(1, 0, 1000, 'First'),
            new SrtBlockNode(2, 1000, 2000, 'Second'),
            new SrtBlockNode(3, 2000, 3000, 'Third'),
        );

        $result = SrtDocumentNodeFactory::create($children);

        self::assertSame(3, $result->count());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCreatePreservesChildrenOrder(): void
    {
        $children = array(
            new SrtBlockNode(1, 0, 1000, 'First'),
            new SrtBlockNode(2, 1000, 2000, 'Second'),
            new SrtBlockNode(3, 2000, 3000, 'Third'),
        );

        $result = SrtDocumentNodeFactory::create($children);

        self::assertSame($children, iterator_to_array($result));
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCreatePreservesExactChildInstances(): void
    {
        $block1 = new SrtBlockNode(1, 0, 1000, 'First');
        $block2 = new SrtBlockNode(2, 1000, 2000, 'Second');
        $children = array($block1, $block2);

        $result = SrtDocumentNodeFactory::create($children);

        $iteratedChildren = iterator_to_array($result);
        self::assertSame($block1, $iteratedChildren[0]);
        self::assertSame($block2, $iteratedChildren[1]);
    }

    /**
     * @param list<SrtBlockNode> $children
     * @param int<0, max> $expectedCount
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    #[Attributes\DataProvider('childrenDataProvider')]
    public function testCreateWithVariousChildrenCounts(array $children, int $expectedCount): void
    {
        $result = SrtDocumentNodeFactory::create($children);

        self::assertSame($expectedCount, $result->count());
        self::assertSame($children, iterator_to_array($result));
    }

    /**
     * @return iterable<string, array{list<SrtBlockNode>, int<0, max>}>
     */
    public static function childrenDataProvider(): iterable
    {
        yield 'empty' => array(
            array(),
            0,
        );

        yield 'single block' => array(
            array(new SrtBlockNode(1, 0, 1000, 'Hello world')),
            1,
        );

        yield 'two blocks' => array(
            array(
                new SrtBlockNode(1, 0, 1000, 'First'),
                new SrtBlockNode(2, 1000, 2000, 'Second'),
            ),
            2,
        );

        yield 'typical subtitle document' => array(
            array(
                new SrtBlockNode(1, 1000, 4000, 'Hello, welcome to the show.'),
                new SrtBlockNode(2, 5000, 8000, 'Today we will discuss...'),
                new SrtBlockNode(3, 9000, 12000, '...something interesting.'),
                new SrtBlockNode(4, 13000, 16000, 'Let\'s get started!'),
            ),
            4,
        );
    }
}
