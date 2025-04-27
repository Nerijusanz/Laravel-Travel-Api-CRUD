<?php

namespace App\Utilities;

class Numbers
{
    public static function generateRandomFloat(int|float|string $minValue, int|float|string $maxValue)
    {
        $value = $minValue + mt_rand() / mt_getrandmax() * ($maxValue - $minValue);

        return Numbers::number_format_decimal($value);
    }

    public static function number_format_decimal(int|float|string $value)
    {
        return number_format(
                        $value,
                        config('app.settings.numbers.decimals.decimal_number'),
                        config('app.settings.numbers.decimals.decimal_separator'),
                        config('app.settings.numbers.decimals.thousands_separator')
                    );
    }

    public static function getAttributeNumberValue(int $value)
    {
        return Numbers::number_format_decimal( ($value / config('app.settings.numbers.attributes.number_value_multiplayer') ) );
    }

    public static function setAttributeNumberValue(int|float|string $value)
    {
        return ( !isset($value) || !is_numeric($value) ) ? 0 : ($value * config('app.settings.numbers.attributes.number_value_multiplayer') );
    }

}