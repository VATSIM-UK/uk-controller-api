<?php

namespace App\Rules\Controller;

use Illuminate\Contracts\Validation\InvokableRule;

class ControllerPositionFrequency implements InvokableRule
{
    private const FREQUENCY_REGEX = '/\d{3}\.(\d{3})/';

    public function __invoke($attribute, $value, $fail)
    {
        $matches = [];
        if (
            !preg_match(self::FREQUENCY_REGEX, $value, $matches) ||
            (int) $matches[1] % 25 !== 0
        ) {
            $fail('Test');
        }
    }
}
