<?php

$img = imagecreatefrompng(__DIR__ . '/../public/img/sanua/hero-boy.png');
$w = imagesx($img);
$h = imagesy($img);
$opaque = 0;
$white = 0;
$transparent = 0;

for ($y = 0; $y < $h; $y += 5) {
    for ($x = 0; $x < $w; $x += 5) {
        $rgba = imagecolorat($img, $x, $y);
        $a = ($rgba >> 24) & 0x7F;
        $r = ($rgba >> 16) & 0xFF;
        $g = ($rgba >> 8) & 0xFF;
        $b = $rgba & 0xFF;

        if ($a >= 100) {
            $transparent++;
        } elseif ($r > 240 && $g > 240 && $b > 240) {
            $white++;
        } else {
            $opaque++;
        }
    }
}

echo "dims={$w}x{$h} opaque={$opaque} white={$white} transparent={$transparent}\n";
