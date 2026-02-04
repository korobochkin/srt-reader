<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Tests\Unit\DataProviders\Ast;

final class SrtBlockNodeDataProvider
{
    /**
     * @psalm-api
     * @return iterable<string, array{int, int, int, string}>
     */
    public static function constructorDataProvider(): iterable
    {
        yield 'typical subtitle block' => array(
            1,
            1000,
            5000,
            'Hello, world!',
        );

        yield 'zero index' => array(
            0,
            0,
            0,
            '',
        );

        yield 'large values' => array(
            999,
            3600000,
            7200000,
            'A very long subtitle text that spans multiple words.',
        );

        yield 'negative times' => array(
            1,
            -1000,
            -500,
            'test',
        );

        yield 'multiline text' => array(
            5,
            10000,
            15000,
            "Line 1\nLine 2\nLine 3",
        );

        yield 'text with special characters' => array(
            1,
            0,
            1000,
            "Special: <i>italic</i> & \"quotes\" 'apostrophe'",
        );

        yield 'unicode text' => array(
            1,
            0,
            1000,
            '日本語テキスト 🎬 émojis',
        );
    }
}
