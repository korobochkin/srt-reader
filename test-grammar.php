<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Korobochkin\SrtReader\SrtGrammarParser;

// Read the full SRT file
$content = fopen(__DIR__ . '/tmp/test.srt', 'r');
$grammar = fopen(__DIR__ . '/grammar/srt.pp', 'r');
$parser = new SrtGrammarParser($grammar);

$count = 0;
foreach ($parser->parseToBlocks($content) as $block) {
    if ($count < 5) {
        echo sprintf(
            "#%d [%d -> %d]: %s\n\n",
            $block->getNumber(),
            $block->getStartTime(),
            $block->getEndTime(),
            $block->getText()
        );
    }
    $count++;
}

echo "Total blocks parsed: $count\n";
