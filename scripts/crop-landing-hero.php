<?php
$src = __DIR__ . '/../public/img/sanua/landing-reference.png';
if (! is_file($src)) { echo "missing\n"; exit(1); }
$img = imagecreatefrompng($src);
$w = imagesx($img);
$h = imagesy($img);
echo "{$w}x{$h}\n";
// crop hero character area ~ right 45% of top 35% of full page mockup
$cw = (int) ($w * 0.42);
$ch = (int) ($h * 0.22);
$cx = (int) ($w * 0.52);
$cy = (int) ($h * 0.055);
$cropped = imagecrop($img, ['x' => $cx, 'y' => $cy, 'width' => $cw, 'height' => $ch]);
if ($cropped) {
    imagepng($cropped, __DIR__ . '/../public/img/sanua/landing-hero-scene.png', 1);
    echo "saved landing-hero-scene.png {$cw}x{$ch}\n";
}
