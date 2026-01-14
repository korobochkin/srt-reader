<?php

declare(strict_types=1);

use Phplrt\Compiler\Compiler;

require __DIR__ . '/../../vendor/autoload.php';

try {
    $source = fopen($sourcePath = __DIR__ . '/../../grammar/srt.pp', 'r');
    if ($source === false) {
        throw new \RuntimeException(sprintf('Failed to open source file: %s', $sourcePath));
    }

    $compiled = fopen($compiledPath = __DIR__ . '/../../grammar/srt.php', 'w+');
    if ($compiled === false) {
        throw new \RuntimeException(sprintf('Failed to open output file: %s', $compiledPath));
    }

    $content = fread($source, filesize($sourcePath));

    if ($content) {
        fwrite(
            $compiled,
            new Compiler()
                ->load($content)
                ->build()
                ->generate()
        );
    } else {
        throw new \RuntimeException(sprintf('Failed to read source file: %s', $sourcePath));
    }
} catch (\Exception $exception) {
    echo 'Error: ' . $exception->getMessage() . \PHP_EOL;
    exit(1);
} finally {
    if (isset($source) && is_resource($source)) {
        fclose($source);
    }
    if (isset($compiled) && is_resource($compiled)) {
        fclose($compiled);
    }
}
