<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Korobochkin\SrtReader\SrtGrammar;
use Korobochkin\SrtReader\SrtParser;

$content = fopen(__DIR__ . '/tmp/test.srt', 'r');

$parser = new SrtParser(SrtGrammar::getGrammar());
$ast = $parser->parse($content);
