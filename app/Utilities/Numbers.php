<?php

namespace App\Utilities;

class Numbers
{

    public static function generateRandomFloat(int|float|string $minValue, int|float|string $maxValue): float
    {

        $minValue = ( !isset($minValue) || !is_numeric($minValue) )? 0 : $minValue;
        $maxValue = ( !isset($maxValue) || !is_numeric($maxValue) )? 0 : $maxValue;

        $value =  $minValue + mt_rand() / mt_getrandmax() * ($maxValue - $minValue);

        return self::number_format_decimal($value);

    }

    public static function number_format_decimal(int|float|string $value): float
    {
        return number_format(
                        floatval($value),
                        config('app.settings.decimal'),
                        config('app.settings.decimal_separator'),
                        config('app.settings.thousands_separator')
                    );
    }

}