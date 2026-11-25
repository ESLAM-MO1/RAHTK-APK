<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

$targets = [
    $root . '/backend/app',
    $root . '/backend/config',
    $root . '/backend/database',
    $root . '/backend/routes',
    $root . '/backend/tests',
    $root . '/backend/lang',
    $root . '/mobile/lib',
    $root . '/mobile/test',
    $root . '/mobile/integration_test',
    $root . '/tooling',
];

$markers = [
    'php' => ['//', '/*', '#'],
    'dart' => ['//', '/*'],
    'sql' => ['--', '/*'],
    'js' => ['//', '/*'],
    'ts' => ['//', '/*'],
    'sh' => ['#'],
    'yml' => ['#'],
    'yaml' => ['#'],
];

$skipDirectories = ['.dart_tool', 'node_modules', 'vendor', 'build', '.git'];
$violations = [];

$walk = function (string $path) use (&$walk, $skipDirectories): \Generator {
    foreach (new \DirectoryIterator($path) as $entry) {
        if ($entry->isDot()) {
            continue;
        }

        if ($entry->isDir()) {
            if (in_array($entry->getFilename(), $skipDirectories, true)) {
                continue;
            }

            yield from $walk($entry->getPathname());

            continue;
        }

        yield $entry->getPathname();
    }
};

foreach ($targets as $target) {
    if (! is_dir($target)) {
        continue;
    }

    foreach ($walk($target) as $file) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (! isset($markers[$extension])) {
            continue;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES);

        if ($lines === false) {
            continue;
        }

        foreach ($lines as $index => $line) {
            $trimmed = ltrim($line);

            if ($extension === 'php' && str_starts_with($trimmed, '#[')) {
                continue;
            }

            if ($extension === 'php' && str_starts_with($trimmed, '#!/')) {
                continue;
            }

            foreach ($markers[$extension] as $marker) {
                if (str_starts_with($trimmed, $marker)) {
                    $violations[] = $file . ':' . ($index + 1);

                    break;
                }
            }
        }
    }
}

if ($violations !== []) {
    fwrite(STDERR, 'Comments are not allowed in source files:' . PHP_EOL);

    foreach ($violations as $violation) {
        fwrite(STDERR, '  ' . $violation . PHP_EOL);
    }

    exit(1);
}

fwrite(STDOUT, 'No source comments found.' . PHP_EOL);

exit(0);
