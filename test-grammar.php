<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Korobochkin\SrtReader\SrtGrammarParser;

// Read the full SRT file
$content = file_get_contents(__DIR__ . '/tmp/test.srt');

$parser = new SrtGrammarParser(__DIR__ . '/grammar/srt.pp');

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
