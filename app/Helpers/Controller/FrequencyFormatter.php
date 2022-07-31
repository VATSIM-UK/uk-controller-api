<?php

namespace App\Helpers\Controller;

class FrequencyFormatter
{
    public static function formatFrequency(float $frequency): string
    {
        return number_format($frequency, '3');
    }
}
