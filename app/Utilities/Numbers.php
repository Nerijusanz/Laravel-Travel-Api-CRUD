<?php

namespace App\Utilities;

class Numbers
{
    public static function generateRandomFloat(int|float|string $minValue, int|float|string $maxValue): int|float|string
    {
        $value = $minValue + mt_rand() / mt_getrandmax() * ($maxValue - $minValue);

        return Numbers::number_format_decimal($value);
    }

    public static function number_format_decimal(int|float|string $value): int|float|string
    {
        return number_format(
                        $value,
                        config('app.settings.numbers.number_format.decimal_number_precision'),
                        config('app.settings.numbers.number_format.decimal_separator'),
                        config('app.settings.numbers.number_format.thousands_separator')
                    );
    }

    public static function getAttributeNumberValue(int|float|string $value): int|float|string
    {
        return Numbers::number_format_decimal( ($value / config('app.settings.numbers.attributes.number_value_multiplayer') ) );
    }

    public static function setAttributeNumberValue(int|float|string $value): int|float|string
    {
        return ( !isset($value) || !is_numeric($value) ) ? 0 : ($value * config('app.settings.numbers.attributes.number_value_multiplayer') );
    }

}