<?php

namespace App\Helpers;

use Spatie\Color\Rgb;
use Spatie\Color\Rgba;
use Filament\Support\Facades\FilamentColor;

class Helpers {
    public static function getColorRgba(string $color, int $intensity, float $alpha = 1): Rgba {
        return Rgba::fromString('rgba(' . FilamentColor::getColors()[$color][$intensity] . ', ' . $alpha .')');
    }

    public static function getColorRgb(string $color, int $intensity): Rgb {
        return Rgb::fromString('rgb(' . FilamentColor::getColors()[$color][$intensity] .')');
    }

}
