<?php

namespace App\Utilities;

class Numbers
{

    public static function generateRandomFloat(int|float|string $minValue, int|float|string $maxValue): float
    {

        $minValue = ( !isset($minValue) || !is_numeric($minValue) )? 0 : $minValue;
        $maxValue = ( !isset($maxValue) || !is_numeric($maxValue) )? 0 : $maxValue;

        $randValue =  $minValue + mt_rand() / mt_getrandmax() * ($maxValue - $minValue);

        return number_format((float)$randValue,config('app.settings.decimal'), '.', '');

    }

    public static function number_format_decimal(float $value): float
    {
        return number_format(
                        floatval($value),
                        config('app.settings.decimal'),
                        config('app.settings.decimal_separator'),
                        config('app.settings.thousands_separator')
                    );
    }

}