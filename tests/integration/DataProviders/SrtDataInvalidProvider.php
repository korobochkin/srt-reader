<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Tests\Integration\DataProviders;

use Korobochkin\SrtReader\Tests\Integration\Abstracts\DataProviders\AbstractSrtDataProvider;
use Phplrt\Parser\Exception\UnrecognizedTokenException;
use Phplrt\Parser\Exception\UnexpectedTokenException;

final class SrtDataInvalidProvider extends AbstractSrtDataProvider
{
    /**
     * @psalm-api
     */
    public static function getInvalid(): array
    {
        return array(
            // Missing T_NEWLINE and T_TEXT
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                )),
                array(
                    UnexpectedTokenException::class,
                    'Syntax error, unexpected end of input, "T_NEWLINE" is expected',
                    0,
                ),
            ),
            // Missing T_NEWLINE and T_TEXT
            array(
                implode(self::RN, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                )),
                array(
                    UnexpectedTokenException::class,
                    'Syntax error, unexpected end of input, "T_NEWLINE" is expected',
                    0,
                ),
            ),
            // Missing T_TEXT for 2nd block
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    'Hello world.',
                    '2',
                    '00:00:00,003 --> 00:00:00,004',
                    '3',
                    '00:00:00,005 --> 00:00:00,006',
                    'Hello mars.',
                )),
                array(
                    UnexpectedTokenException::class,
                    'Syntax error, unexpected "3" (T_INDEX), "T_TEXT" is expected',
                    0,
                ),
            ),
            // Missing T_TEXT for 2nd block
            array(
                implode(self::RN, array(
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    'Hello world.',
                    '2',
                    '00:00:00,003 --> 00:00:00,004',
                    '3',
                    '00:00:00,005 --> 00:00:00,006',
                    'Hello mars.',
                )),
                array(
                    UnexpectedTokenException::class,
                    'Syntax error, unexpected "3" (T_INDEX), "T_TEXT" is expected',
                    0,
                ),
            ),
            // Only whitespace
            array(
                '   ',
                array(
                    UnexpectedTokenException::class,
                    'Syntax error, unexpected " " (T_TEXT), "T_INDEX" is expected',
                    0,
                ),
            ),
            // Only newlines
            array(
                "\n\n\n",
                array(
                    UnexpectedTokenException::class,
                    'Syntax error, unexpected "\n\n" (T_BLANK), "T_INDEX" is expected',
                    0,
                ),
            ),
            // Missing index (starts with timecode)
            array(
                implode(self::N, array(
                    '00:00:00,001 --> 00:00:00,002',
                    'No index.',
                )),
                array(
                    UnexpectedTokenException::class,
                    'Syntax error, unexpected "00:00:00,001" (T_TIMECODE), "T_INDEX" is expected',
                    0,
                ),
            ),
            // Just an index without newline (becomes T_TEXT, not T_INDEX)
            array(
                '1',
                array(
                    UnexpectedTokenException::class,
                    'Syntax error, unexpected "1" (T_TEXT), "T_INDEX" is expected',
                    0,
                ),
            ),
            // Index with newline but no timecode
            array(
                "1\n",
                array(
                    UnexpectedTokenException::class,
                    'Syntax error, unexpected end of input, "T_TIMECODE" is expected',
                    0,
                ),
            ),
            // Timecode without end time
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,001 -->',
                    'Missing end timecode.',
                )),
                array(
                    UnexpectedTokenException::class,
                    'Syntax error, unexpected "\n" (T_NEWLINE), "T_TIMECODE" is expected',
                    0,
                ),
            ),
            // Invalid timecode format (too few digits in milliseconds)
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,01 --> 00:00:00,02',
                    'Invalid milliseconds.',
                )),
                array(
                    UnexpectedTokenException::class,
                    'Syntax error, unexpected "00:00:00,01 --> 00:00:00,02" (T_TEXT), "T_TIMECODE" is expected',
                    0,
                ),
            ),
            // Invalid timecode format (too many digits)
            array(
                implode(self::N, array(
                    '1',
                    '000:00:00,001 --> 00:00:00,002',
                    'Too many hour digits.',
                )),
                array(
                    UnexpectedTokenException::class,
                    'Syntax error, unexpected "000:00:00,001 --> 00:00:00,002" (T_TEXT), "T_TIMECODE" is expected',
                    0,
                ),
            ),
            // Missing arrow between timecodes
            array(
                implode(self::N, array(
                    '1',
                    '00:00:00,001 00:00:00,002',
                    'No arrow.',
                )),
                array(
                    UnrecognizedTokenException::class,
                    'Syntax error, unrecognized " "',
                    0,
                ),
            ),
            // Text before index
            array(
                implode(self::N, array(
                    'Some text',
                    '1',
                    '00:00:00,001 --> 00:00:00,002',
                    'Hello.',
                )),
                array(
                    UnexpectedTokenException::class,
                    'Syntax error, unexpected "Some text" (T_TEXT), "T_INDEX" is expected',
                    0,
                ),
            ),
        );
    }
}
