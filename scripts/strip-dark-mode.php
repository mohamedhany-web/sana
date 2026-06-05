<?php

/**
 * إزالة كلاسات Tailwind dark: وقواعد CSS .dark من قوالب Blade.
 * تشغيل مرة واحدة: php scripts/strip-dark-mode.php
 */

$root = dirname(__DIR__);
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root . '/resources/views', RecursiveDirectoryIterator::SKIP_DOTS)
);

$bladeCount = 0;
$cssLineCount = 0;

foreach ($iterator as $file) {
    if ($file->getExtension() !== 'php' || ! str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }

    $path = $file->getPathname();
    if (str_contains($path, 'force-light-theme.blade.php')) {
        continue;
    }

    $content = file_get_contents($path);
    $original = $content;

    // Tailwind dark: variants (including variants with / in arbitrary values)
    $content = preg_replace('/\s+dark:[a-zA-Z0-9_\[\]\/\.\%\#\-\:\!]+/', '', $content) ?? $content;
    $content = preg_replace("/['\"]dark:[a-zA-Z0-9_\[\]\/\.\\%\\#\\-\\:\\!]+['\"],?\s*/", '', $content) ?? $content;

    // CSS rules starting with .dark or html.dark (single-line blocks)
    $lines = preg_split('/\r\n|\r|\n/', $content);
    $filtered = [];
    foreach ($lines as $line) {
        if (preg_match('/^\s*(\.dark\b|html\.dark\b)/', $line)) {
            $cssLineCount++;
            continue;
        }
        if (preg_match('/DARK MODE|الوضع الداكن/i', $line) && preg_match('/\/\*|^\s*\*/', $line)) {
            continue;
        }
        $filtered[] = $line;
    }
    $content = implode("\n", $filtered);

    // Block comments about dark mode
    $content = preg_replace('/\s*\/\*[^*]*DARK MODE[^*]*\*\/\s*/iu', "\n", $content) ?? $content;

    if ($content !== $original) {
        file_put_contents($path, $content);
        $bladeCount++;
    }
}

echo "Updated {$bladeCount} blade files; removed {$cssLineCount} .dark CSS lines.\n";
