<?php

$src = $argv[1] ?? __DIR__ . '/../public/img/sanua/hero-boy.png';
$dst = $argv[2] ?? $src;

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
$out = imagecreatetruecolor($w, $h);
imagealphablending($out, false);
imagesavealpha($out, true);

$transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
imagefill($out, 0, 0, $transparent);

$threshold = 248;

for ($y = 0; $y < $h; $y++) {
    for ($x = 0; $x < $w; $x++) {
        $rgba = imagecolorat($img, $x, $y);
        $r = ($rgba >> 16) & 0xFF;
        $g = ($rgba >> 8) & 0xFF;
        $b = $rgba & 0xFF;
        $a = ($rgba >> 24) & 0x7F;

        if ($a >= 100 || ($r >= $threshold && $g >= $threshold && $b >= $threshold)) {
            imagesetpixel($out, $x, $y, $transparent);
            continue;
        }

        $color = imagecolorallocatealpha($out, $r, $g, $b, 0);
        imagesetpixel($out, $x, $y, $color);
    }
}

imagepng($out, $dst, 6);
imagedestroy($img);
imagedestroy($out);

echo "Processed {$w}x{$h} -> {$dst}\n";
