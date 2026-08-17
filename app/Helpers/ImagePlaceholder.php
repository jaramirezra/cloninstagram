<?php

namespace App\Helpers;

class ImagePlaceholder
{
    protected static $palette = [
        [52, 73, 94], [231, 76, 60], [241, 196, 15], [46, 204, 113],
        [52, 152, 219], [155, 89, 182], [26, 188, 156], [230, 126, 34],
        [22, 160, 133], [192, 57, 43], [41, 128, 185], [39, 174, 96],
    ];

    public static function make($width, $height, $directory, $filename = null)
    {
        if (!is_dir($directory)) {
            @mkdir($directory, 0777, true);
        }

        $filename = $filename ?: 'img_' . uniqid() . '.png';
        $path = rtrim($directory, '/') . '/' . $filename;

        $top = self::$palette[mt_rand(0, count(self::$palette) - 1)];
        $bottom = self::$palette[mt_rand(0, count(self::$palette) - 1)];

        $img = imagecreatetruecolor($width, $height);

        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / max(1, $height - 1);
            $r = (int)round($top[0] + ($bottom[0] - $top[0]) * $ratio);
            $g = (int)round($top[1] + ($bottom[1] - $top[1]) * $ratio);
            $b = (int)round($top[2] + ($bottom[2] - $top[2]) * $ratio);
            $color = imagecolorallocate($img, $r, $g, $b);
            imageline($img, 0, $y, $width, $y, $color);
        }

        $white = imagecolorallocatealpha($img, 255, 255, 255, 85);
        $softer = imagecolorallocatealpha($img, 255, 255, 255, 110);

        $cx = (int)($width * (0.20 + mt_rand(0, 60) / 100));
        $cy = (int)($height * (0.20 + mt_rand(0, 60) / 100));
        $radius = (int)(min($width, $height) / 3);
        imagefilledellipse($img, $cx, $cy, $radius * 2, $radius * 2, $white);

        $lx = (int)($width * (0.15 + mt_rand(0, 70) / 100));
        $ly = (int)($height * (0.15 + mt_rand(0, 70) / 100));
        imagefilledellipse($img, $lx, $ly, (int)($radius * 0.55), (int)($radius * 0.55), $softer);

        imagepng($img, $path);
        imagedestroy($img);

        return $filename;
    }
}