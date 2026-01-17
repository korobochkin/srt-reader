<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Korobochkin\SrtReader\SrtGrammar;
use Korobochkin\SrtReader\SrtParser;

try {
    //$content = fopen(__DIR__ . '/../../tmp/broken.srt', 'r');
    $content = fopen(__DIR__ . '/../../tmp/test.srt', 'r');

    if (!is_resource($content)) {
        throw new \RuntimeException('Failed to open content file');
    }

    $parser = new SrtParser(SrtGrammar::getGrammar());
    $ast = $parser->parse($content);

    foreach ($ast as $block) {
        echo 'The block #' . $block->getIndex() . '. With the text: ' . $block->getText() . \PHP_EOL;
    }
} catch (\Exception $exception) {
    echo "Parse error: " . $exception->getMessage() . \PHP_EOL;
    exit(1);
} finally {
    if (isset($content) && is_resource($content)) {
        fclose($content);
    }
}
