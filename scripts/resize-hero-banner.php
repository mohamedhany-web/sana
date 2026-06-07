<?php

$src = __DIR__ . '/../public/img/sanua/hero-banner.png';
$dst = __DIR__ . '/../public/img/sanua/hero-banner-1280x300.png';

$img = imagecreatefrompng($src);
$sw = imagesx($img);
$sh = imagesy($img);
$tw = 1280;
$th = 300;
$tr = $tw / $th;
$sr = $sw / $sh;

if ($sr > $tr) {
    $nh = $sh;
    $nw = (int) ($sh * $tr);
    $sx = (int) (($sw - $nw) / 2);
    $sy = 0;
} else {
    $nw = $sw;
    $nh = (int) ($sw / $tr);
    $sx = 0;
    $sy = (int) (($sh - $nh) / 2);
}

$out = imagecreatetruecolor($tw, $th);
imagealphablending($out, false);
imagesavealpha($out, true);
imagecopyresampled($out, $img, 0, 0, $sx, $sy, $tw, $th, $nw, $nh);
imagepng($out, $dst, 6);
imagedestroy($img);
imagedestroy($out);

echo "Saved {$tw}x{$th} to {$dst}\n";
