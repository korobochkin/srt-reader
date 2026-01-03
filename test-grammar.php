<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Korobochkin\SrtReader\SrtGrammar;
use Korobochkin\SrtReader\SrtParser;

//$content = fopen(__DIR__ . '/tmp/broken.srt', 'r');
$content = fopen(__DIR__ . '/tmp/test.srt', 'r');

try {
    $parser = new SrtParser(SrtGrammar::getGrammar());
    $ast = $parser->parse($content);
    $a = 1;
} catch (\Exception $exception) {
    echo "Parse error: " . $exception->getMessage() . \PHP_EOL;
}
