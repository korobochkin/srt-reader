<?php

declare(strict_types=1);

// @generated - sync with SrtGrammar class when updating

/**
 * @var array{
 *      initial: array-key,
 *      tokens: array{
 *        default: array<non-empty-string, non-empty-string>
 *      },
 *      skip: list<non-empty-string>,
 *      grammar: array<array-key, \Phplrt\Parser\Grammar\RuleInterface>,
 *      reducers: array{
 *        Block: callable(\Phplrt\Parser\Context, array{0: \Phplrt\Lexer\Token\Token, 1: \Phplrt\Lexer\Token\Composite, 2: \Phplrt\Lexer\Token\Composite, 3: \Phplrt\Lexer\Token\Token, ...<int, \Phplrt\Lexer\Token\Token>}):\Korobochkin\SrtReader\Ast\SrtBlockNode,
 *        Document: callable(\Phplrt\Parser\Context, list<\Korobochkin\SrtReader\Ast\SrtBlockNode>):\Korobochkin\SrtReader\Ast\SrtDocumentNode
 *      },
 *      transitions?: array<array-key, mixed>
 *  }
 */
return array(
    'initial' => 'Document',
    'tokens' => array(
        'default' => array(
            'T_BOM' => '\\x{FEFF}',
            'T_TIMECODE' => '(\\d{2})[,.:，．。：](\\d{2})[,.:，．。：](\\d{2})[,.:，．。：](\\d{3})',
            'T_ARROW' => '\\h*-->\\h*',
            'T_INDEX' => '(?<=^)\\d+(?=\\r?\\n)',
            'T_BLANK' => '\\r?\\n\\r?\\n',
            'T_NEWLINE' => '\\r?\\n',
            'T_TEXT' => '(?<=^)[^\\r\\n]+',
        ),
    ),
    'skip' => array(
        'T_BOM',
    ),
    'transitions' => array(),
    'grammar' => array(
        0 => new \Phplrt\Parser\Grammar\Lexeme('T_BLANK', false),
        1 => new \Phplrt\Parser\Grammar\Lexeme('T_INDEX', true),
        2 => new \Phplrt\Parser\Grammar\Lexeme('T_NEWLINE', false),
        3 => new \Phplrt\Parser\Grammar\Lexeme('T_NEWLINE', false),
        4 => new \Phplrt\Parser\Grammar\Optional(0),
        5 => new \Phplrt\Parser\Grammar\Lexeme('T_TIMECODE', true),
        6 => new \Phplrt\Parser\Grammar\Lexeme('T_ARROW', false),
        7 => new \Phplrt\Parser\Grammar\Lexeme('T_TIMECODE', true),
        8 => new \Phplrt\Parser\Grammar\Concatenation(array(10, 11)),
        9 => new \Phplrt\Parser\Grammar\Lexeme('T_NEWLINE', false),
        10 => new \Phplrt\Parser\Grammar\Lexeme('T_TEXT', true),
        11 => new \Phplrt\Parser\Grammar\Optional(9),
        'Block' => new \Phplrt\Parser\Grammar\Concatenation(array(1, 2, 'Timecode', 3, 'TextLines', 4)),
        'Document' => new \Phplrt\Parser\Grammar\Repetition('Block', 0, \INF),
        'TextLines' => new \Phplrt\Parser\Grammar\Repetition(8, 1, \INF),
        'Timecode' => new \Phplrt\Parser\Grammar\Concatenation(array(5, 6, 7)),
    ),
    'reducers' => array(
        'Block' => static fn(\Phplrt\Parser\Context $ctx, $children) => \Korobochkin\SrtReader\Ast\SrtBlockNodeFactory::create($children),
        'Document' => static fn(\Phplrt\Parser\Context $ctx, $children) => \Korobochkin\SrtReader\Ast\SrtDocumentNodeFactory::create($children),
    ),
);
