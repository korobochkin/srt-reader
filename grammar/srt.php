<?php

declare(strict_types=1);

/**
 * @var array{
 *     initial: array-key,
 *     tokens: array{
 *         default: array<non-empty-string, non-empty-string>,
 *         ...
 *     },
 *     skip: list<non-empty-string>,
 *     grammar: array<array-key, \Phplrt\Parser\Grammar\RuleInterface>,
 *     reducers: array<array-key, callable(\Phplrt\Parser\Context, mixed):mixed>,
 *     transitions?: array<array-key, mixed>
 * }
 */
return [
    'initial' => 'Document',
    'tokens' => [
        'default' => [
            'T_BOM' => '\\x{FEFF}',
            'T_TIMECODE' => '\\d{2}:\\d{2}:\\d{2},\\d{3}',
            'T_ARROW' => '\\h*-->\\h*',
            'T_NUMBER' => '(?<=^)\\d+(?=\\r?\\n)',
            'T_BLANK' => '\\r?\\n\\r?\\n',
            'T_NEWLINE' => '\\r?\\n',
            'T_TEXT' => '(?-s).+',
        ],
    ],
    'skip' => [
        'T_BOM',
    ],
    'transitions' => [],
    'grammar' => [
        0 => new \Phplrt\Parser\Grammar\Lexeme('T_BLANK', false),
        1 => new \Phplrt\Parser\Grammar\Lexeme('T_NUMBER', true),
        2 => new \Phplrt\Parser\Grammar\Lexeme('T_NEWLINE', false),
        3 => new \Phplrt\Parser\Grammar\Lexeme('T_NEWLINE', false),
        4 => new \Phplrt\Parser\Grammar\Optional(0),
        5 => new \Phplrt\Parser\Grammar\Lexeme('T_TIMECODE', true),
        6 => new \Phplrt\Parser\Grammar\Lexeme('T_ARROW', false),
        7 => new \Phplrt\Parser\Grammar\Lexeme('T_TIMECODE', true),
        8 => new \Phplrt\Parser\Grammar\Concatenation([10, 11]),
        9 => new \Phplrt\Parser\Grammar\Lexeme('T_NEWLINE', false),
        10 => new \Phplrt\Parser\Grammar\Lexeme('T_TEXT', true),
        11 => new \Phplrt\Parser\Grammar\Optional(9),
        'Block' => new \Phplrt\Parser\Grammar\Concatenation([1, 2, 'Timecode', 3, 'TextLines', 4]),
        'Document' => new \Phplrt\Parser\Grammar\Repetition('Block', 0, \INF),
        'TextLines' => new \Phplrt\Parser\Grammar\Repetition(8, 1, \INF),
        'Timecode' => new \Phplrt\Parser\Grammar\Concatenation([5, 6, 7]),
    ],
    'reducers' => [
        'Block' => static function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            // The "$state" variable is an auto-generated
            $state = $ctx->state;

            return new \Korobochkin\SrtReader\Ast\SrtBlockNode($state, $children, $offset);
        },
        'Document' => static function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            // The "$state" variable is an auto-generated
            $state = $ctx->state;

            return new \Korobochkin\SrtReader\Ast\SrtDocumentNode($state, $children, $offset);
        },
    ],
];
