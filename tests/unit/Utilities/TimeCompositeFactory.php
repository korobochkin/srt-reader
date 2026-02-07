<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Tests\Unit\Utilities;

use Phplrt\Lexer\Token\Composite;
use Phplrt\Lexer\Token\Token;

final class TimeCompositeFactory
{
    /**
     * Creates a Composite token representing a timecode value.
     *
     * Structure matches the real lexer output where all child tokens
     * have zero-padded string values (HH, MM, SS as 2 digits, ms as 3 digits).
     *
     * @param int<0, max> $hours
     * @param int<0, max> $minutes
     * @param int<0, max> $seconds
     * @param int<0, max> $milliseconds
     * @return Composite
     */
    public static function createTimeComposite(
        int $hours,
        int $minutes,
        int $seconds,
        int $milliseconds,
    ): Composite {
        return new Composite(
            'T_TIMECODE',
            \sprintf('%02d:%02d:%02d,%03d', $hours, $minutes, $seconds, $milliseconds),
            0,
            array(
                new Token('T_TIMECODE', \sprintf('%02d', $hours), 0),
                new Token('T_TIMECODE', \sprintf('%02d', $minutes), 0),
                new Token('T_TIMECODE', \sprintf('%02d', $seconds), 0),
                new Token('T_TIMECODE', \sprintf('%03d', $milliseconds), 0),
            ),
        );
    }
}
