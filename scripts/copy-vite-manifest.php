<?php

$src = __DIR__.'/../public/build/manifest.json';
$dir = __DIR__.'/../api/build';

if (! is_file($src)) {
    fwrite(STDERR, "Vite manifest not found at {$src}\n");
    exit(1);
}

if (! is_dir($dir) && ! mkdir($dir, 0777, true) && ! is_dir($dir)) {
    fwrite(STDERR, "Failed to create directory {$dir}\n");
    exit(1);
}

if (! copy($src, $dir.'/manifest.json')) {
    fwrite(STDERR, "Failed to copy manifest to {$dir}/manifest.json\n");
    exit(1);
}
