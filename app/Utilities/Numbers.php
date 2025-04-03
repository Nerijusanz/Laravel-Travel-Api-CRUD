<?php

namespace App\Utilities;

class Numbers
{

    function generateRandomFloat(int|float|string $minValue, int|float|string $maxValue): float
    {
        $minValue = ( !isset($minValue) || !is_numeric($minValue) )? 0 : $minValue;
        $maxValue = ( !isset($maxValue) || !is_numeric($maxValue) )? 0 : $maxValue;

        $randValue =  $minValue + mt_rand() / mt_getrandmax() * ($maxValue - $minValue);

        return number_format($randValue,2);
    }

}
?>