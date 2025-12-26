<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader;

use Phplrt\Compiler\Compiler;
use Phplrt\Compiler\Runtime\PrintableNode;
use Phplrt\Lexer\Token\Token;

class SrtGrammarParser
{
    private Compiler $compiler;

    /**
     * @param resource $grammar
     */
    public function __construct($grammar)
    {
        $this->compiler = new Compiler();
        $this->compiler->load($grammar);
    }

    /**
     * @param resource $content
     * @return \Generator<SrtBlock>
     */
    public function parseToBlocks($content): \Generator
    {
        $ast = $this->parse($content);

        foreach ($ast->children as $blockNode) {
            if ($blockNode instanceof PrintableNode && $blockNode->getState() === 'Block') {
                yield $this->nodeToBlock($blockNode);
            }
        }
    }

    /**
     * @param resource $content
     */
    public function parse($content): PrintableNode
    {
        return $this->compiler->parse($content);
    }

    /**
     * Convert an AST Block node to SrtBlock
     */
    private function nodeToBlock(PrintableNode $node): SrtBlock
    {
        $number = 0;
        $startTime = 0;
        $endTime = 0;
        $textLines = array();

        foreach ($node->children as $child) {
            if ($child instanceof Token) {
                if ($child->getName() === 'T_NUMBER') {
                    $number = (int) $child->getValue();
                }
            } elseif ($child instanceof PrintableNode) {
                if ($child->getState() === 'Timecode') {
                    [$startTime, $endTime] = $this->extractTimecode($child);
                } elseif ($child->getState() === 'TextLines') {
                    $textLines = $this->extractTextLines($child);
                }
            }
        }

        $text = implode("\n", $textLines);

        return new SrtBlock($number, $startTime, $endTime, $text);
    }

    /**
     * Extract start and end time from Timecode node
     *
     * @return array{0: int, 1: int}
     */
    private function extractTimecode(PrintableNode $node): array
    {
        $times = array();
        foreach ($node->children as $child) {
            if ($child instanceof Token && $child->getName() === 'T_TIMECODE') {
                $times[] = $this->parseTimecode($child->getValue());
            }
        }

        return array($times[0] ?? 0, $times[1] ?? 0);
    }

    /**
     * Convert "00:01:23,551" to milliseconds
     */
    private function parseTimecode(string $time): int
    {
        [$hms, $ms] = explode(',', $time);
        [$hours, $minutes, $seconds] = explode(':', $hms);

        return ((int) $hours * 3600000)
            + ((int) $minutes * 60000)
            + ((int) $seconds * 1000)
            + (int) $ms;
    }

    /**
     * Extract text lines from TextLines node
     *
     * @return array<string>
     */
    private function extractTextLines(PrintableNode $node): array
    {
        $lines = array();
        foreach ($node->children as $child) {
            if ($child instanceof PrintableNode && $child->getState() === 'TextLine') {
                foreach ($child->children as $token) {
                    if ($token instanceof Token && $token->getName() === 'T_TEXT') {
                        $lines[] = $token->getValue();
                    }
                }
            }
        }

        return $lines;
    }
}
