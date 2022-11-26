<?php

namespace App\Rules\Airfield;

use Illuminate\Contracts\Validation\Rule;

class AirfieldIcao implements Rule
{
    const AIRFIELD_REGEX = '/^[0-9A-Z]{4}$/';
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
        return is_string($value) && preg_match(self::AIRFIELD_REGEX, $value) === self::REGEX_MATCHED;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.airfield_icao');
    }
}
