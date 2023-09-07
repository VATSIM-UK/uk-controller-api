<?php

namespace App\Rules\Controller;

use Illuminate\Contracts\Validation\InvokableRule;

class ControllerPositionFrequency implements InvokableRule
{
    private const FREQUENCY_REGEX = '/^\d{3}\.(\d{3})$/';

    public function __invoke($attribute, $value, $fail)
    {
        $matches = [];
        if (
            !is_string($value) ||
            empty($value) ||
            !preg_match(self::FREQUENCY_REGEX, $value, $matches) ||
            (int) $matches[1] % 5 !== 0
        ) {
            $fail('validation.frequency')->translate();
        }
    }
}
