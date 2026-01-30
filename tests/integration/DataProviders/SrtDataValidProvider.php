<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Tests\Integration\DataProviders;

use Korobochkin\SrtReader\Ast\SrtBlockNode;
use Korobochkin\SrtReader\Ast\SrtDocumentNode;
use Korobochkin\SrtReader\Tests\Integration\Abstracts\DataProviders\AbstractSrtDataProvider;

final class SrtDataValidProvider extends AbstractSrtDataProvider
{
    /**
     * @psalm-api
     */
    public static function getValid(): array
    {
        return array(
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    'Hello world.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, 'Hello world.'),
                )),
            ),
            array(
                implode(self::RN, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    'Hello world.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, 'Hello world.'),
                )),
            ),
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    'Hello world.',
                    'Hello universe.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, 'Hello world. Hello universe.'),
                )),
            ),
            array(
                implode(self::RN, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    'Hello world.',
                    'Hello universe.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, 'Hello world. Hello universe.'),
                )),
            ),
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    'Hello world.',
                    '2',
                    '00:00:00,003 --> 00:00:00,004',
                    'Hello universe.',
                    '3',
                    '00:00:00,005 --> 00:00:00,006',
                    'Hello mars.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, 'Hello world.'),
                    new SrtBlockNode(2, 3, 4, 'Hello universe.'),
                    new SrtBlockNode(3, 5, 6, 'Hello mars.'),
                )),
            ),
            array(
                implode(self::RN, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    'Hello world.',
                    '2',
                    '00:00:00,003 --> 00:00:00,004',
                    'Hello universe.',
                    '3',
                    '00:00:00,005 --> 00:00:00,006',
                    'Hello mars.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, 'Hello world.'),
                    new SrtBlockNode(2, 3, 4, 'Hello universe.'),
                    new SrtBlockNode(3, 5, 6, 'Hello mars.'),
                )),
            ),
            array(
                implode('', array(
                    '1',
                    self::N,
                    '00:00:00,001 --> 00:00:00,002',
                    self::N,
                    'Hello world.',
                    self::N,
                    self::N,
                    '2',
                    self::N,
                    '00:00:00,003 --> 00:00:00,004',
                    self::N,
                    'Hello universe.',
                    self::N,
                    self::N,
                    '3',
                    self::N,
                    '00:00:00,005 --> 00:00:00,006',
                    self::N,
                    'Hello mars.',
                    self::N,
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, 'Hello world.'),
                    new SrtBlockNode(2, 3, 4, 'Hello universe.'),
                    new SrtBlockNode(3, 5, 6, 'Hello mars.'),
                )),
            ),
            array(
                implode('', array(
                    '1',
                    self::RN,
                    '00:00:00,001 --> 00:00:00,002',
                    self::RN,
                    'Hello world.',
                    self::RN,
                    self::RN,
                    '2',
                    self::RN,
                    '00:00:00,003 --> 00:00:00,004',
                    self::RN,
                    'Hello universe.',
                    self::RN,
                    self::RN,
                    '3',
                    self::RN,
                    '00:00:00,005 --> 00:00:00,006',
                    self::RN,
                    'Hello mars.',
                    self::RN,
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, 'Hello world.'),
                    new SrtBlockNode(2, 3, 4, 'Hello universe.'),
                    new SrtBlockNode(3, 5, 6, 'Hello mars.'),
                )),
            ),
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    '1)23 One Two Three',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, '1)23 One Two Three'),
                )),
            ),
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    '1)23',
                    '2',
                    '00:00:00,003 --> 00:00:00,004',
                    'One Two Three',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, '1)23'),
                    new SrtBlockNode(2, 3, 4, 'One Two Three'),
                )),
            ),
            // Large index number
            array(
                implode(self::N, array(
                    '9999',
                    '00:00:00,001 --> 00:00:00,002',
                    'Large index.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(9999, 1, 2, 'Large index.'),
                )),
            ),
            // Very large index number
            array(
                implode(self::N, array(
                    \PHP_INT_MAX,
                    '00:00:00,001 --> 00:00:00,002',
                    'Very large index.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(\PHP_INT_MAX, 1, 2, 'Very large index.'),
                )),
            ),
            // Maximum timecode boundary
            array(
                implode(self::N, array(
                    '1',
                    '99:59:59,998 --> 99:59:59,999',
                    'Max timecode.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 359999998, 359999999, 'Max timecode.'),
                )),
            ),
            // Hours exceeding 24
            array(
                implode(self::N, array(
                    '1',
                    '25:30:45,123 --> 26:00:00,000',
                    'Long video timecode.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 91845123, 93600000, 'Long video timecode.'),
                )),
            ),
            // Period as timecode separator
            array(
                implode(self::N, array(
                    '1',
                    '00:00:01.500 --> 00:00:02.750',
                    'Period separator.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1500, 2750, 'Period separator.'),
                )),
            ),
            // HTML formatting tags in text
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    '<i>Italic text</i>',
                    '2',
                    '00:00:00,003 --> 00:00:00,004',
                    '<b>Bold</b> and <u>underline</u>',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, '<i>Italic text</i>'),
                    new SrtBlockNode(2, 3, 4, '<b>Bold</b> and <u>underline</u>'),
                )),
            ),
            // Unicode characters in text
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    '日本語テスト',
                    '2',
                    '00:00:00,003 --> 00:00:00,004',
                    'Émojis: 🎬 🎥 📺',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, '日本語テスト'),
                    new SrtBlockNode(2, 3, 4, 'Émojis: 🎬 🎥 📺'),
                )),
            ),
            // Many text lines (4 lines)
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    'Line one.',
                    'Line two.',
                    'Line three.',
                    'Line four.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, 'Line one. Line two. Line three. Line four.'),
                )),
            ),
            // Text containing arrow-like sequence (should not be parsed as arrow)
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    'Go to --> the next scene.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, 'Go to --> the next scene.'),
                )),
            ),
            // Extra whitespace around arrow
            array(
                implode(self::N, array(
                    '1',
                    '00:00:01,000    -->    00:00:02,000',
                    'Extra spaces around arrow.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1000, 2000, 'Extra spaces around arrow.'),
                )),
            ),
            // Text that looks like an index but is clearly text
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    '123 bottles of beer on the wall',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, '123 bottles of beer on the wall'),
                )),
            ),
            // Complex timecode calculation (01:23:45,678 = 5025678 ms)
            array(
                implode(self::N, array(
                    '1',
                    '01:23:45,678 --> 02:34:56,789',
                    'Complex timecode.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 5025678, 9296789, 'Complex timecode.'),
                )),
            ),
            // Zero timecode
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,000 --> 00:00:00,001',
                    'Starts at zero.',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 0, 1, 'Starts at zero.'),
                )),
            ),
            // Text with special punctuation
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    '- What?! "Really?" he said...',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(1, 1, 2, '- What?! "Really?" he said...'),
                )),
            ),
            // Multiple consecutive blocks with complex content
            array(
                implode(self::N, array(
                    '100',
                    '10:00:00,000 --> 10:00:05,000',
                    'First speaker:',
                    '- Hello there!',
                    '',
                    '101',
                    '10:00:05,001 --> 10:00:10,000',
                    'Second speaker:',
                    '- Hi! How are you?',
                )),
                new SrtDocumentNode(array(
                    new SrtBlockNode(100, 36000000, 36005000, 'First speaker: - Hello there!'),
                    new SrtBlockNode(101, 36005001, 36010000, 'Second speaker: - Hi! How are you?'),
                )),
            ),
        );
    }
}
