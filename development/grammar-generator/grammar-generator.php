<?php

declare(strict_types=1);

use Phplrt\Compiler\Compiler;

require __DIR__ . '/../../vendor/autoload.php';

try {
    $isError = false;
    $source = fopen($sourcePath = __DIR__ . '/../../grammar/srt.pp', 'r');
    $sourceFileSize = filesize($sourcePath);
    if ($source === false || $sourceFileSize === false || $sourceFileSize < 1) {
        throw new \RuntimeException(sprintf('Failed to open source file: %s', $sourcePath));
    }

    $compiled = fopen($compiledPath = __DIR__ . '/../../grammar/srt.php', 'w+');
    if ($compiled === false) {
        throw new \RuntimeException(sprintf('Failed to open output file: %s', $compiledPath));
    }

    $content = fread($source, $sourceFileSize);

    if ($content === false || $content === '') {
        throw new \RuntimeException(sprintf('Failed to read source file: %s', $sourcePath));
    }

    fwrite(
        $compiled,
        new Compiler()
            ->load($content)
            ->build()
            ->generate()
    );
} catch (\Exception $exception) {
    $isError = true;
    echo 'Error: ' . $exception->getMessage() . \PHP_EOL;
} finally {
    /** @psalm-suppress RedundantCondition */
    if (isset($source) && is_resource($source)) {
        fclose($source);
    }

    /** @psalm-suppress RedundantCondition */
    if (isset($compiled) && is_resource($compiled)) {
        fclose($compiled);
    }
}

exit($isError ? 1 : 0);
