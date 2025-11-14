<?php
// Quick PHP syntax scanner. Run from project root:
// php tools/check_syntax.php

$root = realpath(__DIR__ . '/../');
$skipDirs = ['vendor', 'storage', 'node_modules', 'public' . DIRECTORY_SEPARATOR . 'build'];

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$errors = [];

foreach ($it as $file) {
    if (! $file->isFile()) {
        continue;
    }
    $path = $file->getPathname();
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    if (strtolower($ext) !== 'php') {
        continue;
    }
    // skip big/third-party dirs
    foreach ($skipDirs as $d) {
        if (str_contains($path, DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR)) {
            continue 2;
        }
    }

    // run php -l (lint)
    $cmd = 'php -l ' . escapeshellarg($path) . ' 2>&1';
    $output = shell_exec($cmd);
    if ($output === null) {
        echo "Cannot run shell_exec(); run `php -l` manually on your PHP files.\n";
        exit(1);
    }

    if (! str_contains($output, 'No syntax errors detected')) {
        $errors[$path] = trim($output);
    }
}

if (empty($errors)) {
    echo "No PHP syntax errors found (php -l).\n";
    exit(0);
}

echo "Syntax errors found:\n\n";
foreach ($errors as $file => $msg) {
    echo "File: {$file}\n";
    echo $msg . "\n\n";
}

echo "Fix the reported file(s) then re-run: php tools/check_syntax.php\n";
