<?php

/**
 * Strip white/near-white background, soften fringe, and tight-crop hero character PNG.
 *
 * Usage: php scripts/process-hero-boy.php [src] [dst]
 */

$src = $argv[1] ?? __DIR__ . '/../public/img/sanua/hero-boy-raw.png';
$dst = $argv[2] ?? __DIR__ . '/../public/img/sanua/hero-boy.png';

if (! is_file($src)) {
    fwrite(STDERR, "Missing: {$src}\n");
    exit(1);
}

$img = imagecreatefrompng($src);
if (! $img) {
    fwrite(STDERR, "Cannot read PNG\n");
    exit(1);
}

$w = imagesx($img);
$h = imagesy($img);

$isNearWhite = static function (int $r, int $g, int $b, int $a, int $hard = 235, int $soft = 210): float {
    if ($a >= 100) {
        return 1.0;
    }

    $min = min($r, $g, $b);
    $max = max($r, $g, $b);
    $avg = ($r + $g + $b) / 3;
    $sat = $max - $min;

    if ($min >= $hard && $sat <= 18) {
        return 1.0;
    }

    if ($avg >= $soft && $sat <= 28) {
        return min(1.0, ($avg - $soft) / max(1, $hard - $soft));
    }

    return 0.0;
};

// Flood-fill from edges through near-white pixels.
$visited = array_fill(0, $h, array_fill(0, $w, false));
$queue = [];

$pushIfWhite = static function (int $x, int $y) use (&$queue, &$visited, $img, $w, $h, $isNearWhite): void {
    if ($x < 0 || $y < 0 || $x >= $w || $y >= $h || $visited[$y][$x]) {
        return;
    }

    $rgba = imagecolorat($img, $x, $y);
    $r = ($rgba >> 16) & 0xFF;
    $g = ($rgba >> 8) & 0xFF;
    $b = $rgba & 0xFF;
    $a = ($rgba >> 24) & 0x7F;

    if ($isNearWhite($r, $g, $b, $a) < 1.0) {
        return;
    }

    $visited[$y][$x] = true;
    $queue[] = [$x, $y];
};

for ($x = 0; $x < $w; $x++) {
    $pushIfWhite($x, 0);
    $pushIfWhite($x, $h - 1);
}
for ($y = 0; $y < $h; $y++) {
    $pushIfWhite(0, $y);
    $pushIfWhite($w - 1, $y);
}

while ($queue !== []) {
    [$x, $y] = array_shift($queue);
    $pushIfWhite($x - 1, $y);
    $pushIfWhite($x + 1, $y);
    $pushIfWhite($x, $y - 1);
    $pushIfWhite($x, $y + 1);
}

$out = imagecreatetruecolor($w, $h);
imagealphablending($out, false);
imagesavealpha($out, true);
$transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
imagefill($out, 0, 0, $transparent);

$minX = $w;
$minY = $h;
$maxX = 0;
$maxY = 0;

for ($y = 0; $y < $h; $y++) {
    for ($x = 0; $x < $w; $x++) {
        if ($visited[$y][$x]) {
            imagesetpixel($out, $x, $y, $transparent);
            continue;
        }

        $rgba = imagecolorat($img, $x, $y);
        $r = ($rgba >> 16) & 0xFF;
        $g = ($rgba >> 8) & 0xFF;
        $b = $rgba & 0xFF;
        $a = ($rgba >> 24) & 0x7F;

        $whiteMix = $isNearWhite($r, $g, $b, $a);
        if ($whiteMix >= 1.0) {
            imagesetpixel($out, $x, $y, $transparent);
            continue;
        }

        if ($whiteMix > 0) {
            $alpha = (int) round(127 * $whiteMix);
            $color = imagecolorallocatealpha($out, $r, $g, $b, min(127, $alpha + $a));
        } else {
            $color = imagecolorallocatealpha($out, $r, $g, $b, $a);
        }

        imagesetpixel($out, $x, $y, $color);

        if ($a < 100 && $whiteMix < 0.35) {
            $minX = min($minX, $x);
            $minY = min($minY, $y);
            $maxX = max($maxX, $x);
            $maxY = max($maxY, $y);
        }
    }
}

if ($maxX <= $minX || $maxY <= $minY) {
    fwrite(STDERR, "No opaque pixels found\n");
    exit(1);
}

$pad = (int) round(min($w, $h) * 0.015);
$minX = max(0, $minX - $pad);
$minY = max(0, $minY - $pad);
$maxX = min($w - 1, $maxX + $pad);
$maxY = min($h - 1, $maxY + $pad);

$cw = $maxX - $minX + 1;
$ch = $maxY - $minY + 1;

$cropped = imagecreatetruecolor($cw, $ch);
imagealphablending($cropped, false);
imagesavealpha($cropped, true);
imagefill($cropped, 0, 0, $transparent);
imagecopy($cropped, $out, 0, 0, $minX, $minY, $cw, $ch);

imagepng($cropped, $dst, 1);
imagedestroy($img);
imagedestroy($out);
imagedestroy($cropped);

echo "Processed {$w}x{$h} -> cropped {$cw}x{$ch} -> {$dst}\n";
