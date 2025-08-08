<?php

namespace App\Helpers;

class ColorHelper
{
    public static function getTextColor(string $hex): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 6) {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));

            $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
            return $brightness > 128 ? '#000000' : '#FFFFFF';
        }

        return '#000000'; // default jika hex tidak valid
    }
}
