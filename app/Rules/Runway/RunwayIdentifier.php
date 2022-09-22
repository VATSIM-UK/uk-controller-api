<?php

namespace App\Rules\Runway;

use Illuminate\Contracts\Validation\Rule;

class RunwayIdentifier implements Rule
{
    const RUNWAY_REGEX = '/^(0[1-9]|[1-2]\d|3[0-6])[LCRG]?$/';
    const REGEX_MATCHED = 1;

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return is_string($value) && preg_match(self::RUNWAY_REGEX, $value) === self::REGEX_MATCHED;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.runways.identifier');
    }
}
