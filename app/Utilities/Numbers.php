<?php

namespace App\Utilities;

class Numbers
{
    public static function generateRandomFloat(int|float|string $minValue, int|float|string $maxValue)
    {
        $minValue = ( !isset($minValue) || !is_numeric($minValue) )? 0 : $minValue;
        $maxValue = ( !isset($maxValue) || !is_numeric($maxValue) )? 0 : $maxValue;

        $value =  $minValue + mt_rand() / mt_getrandmax() * ($maxValue - $minValue);

        return Numbers::number_format_decimal($value);
    }

    public static function number_format_decimal(int|float|string $value)
    {
        return number_format(
                        $value,
                        config('app.settings.decimal'),
                        config('app.settings.decimal_separator'),
                        config('app.settings.thousands_separator')
                    );
    }

    public static function getAttributeNumberValue(int $value)
    {
        return Numbers::number_format_decimal( ($value / config('app.settings.attribute_number_value_multiplayer') ) );
    }

    public static function setAttributeNumberValue(int|float|string $value)
    {
        return ( !isset($value) || !is_numeric($value) ) ? 0 : ($value * config('app.settings.attribute_number_value_multiplayer') );
    }

}