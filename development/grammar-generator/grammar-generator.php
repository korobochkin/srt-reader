<?php

declare(strict_types=1);

use Phplrt\Compiler\Compiler;

require __DIR__ . '/../../vendor/autoload.php';

try {
    $source = fopen($sourcePath = __DIR__ . '/../../grammar/srt.pp', 'r');
    $compiled = fopen(__DIR__ . '/../../grammar/srt.php', 'w+');

    fwrite(
        $compiled,
        new Compiler()
            ->load(fread($source, filesize($sourcePath)))
            ->build()
            ->generate()
    );
} finally {
    if (isset($source) && is_resource($source)) {
        fclose($source);
    }
    if (isset($compiled) && is_resource($compiled)) {
        fclose($compiled);
    }
}
