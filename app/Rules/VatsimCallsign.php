<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class VatsimCallsign implements Rule
{
    const CALLSIGN_REGEX = '^[A-Za-z0-9\-_]{1,10}$';
    const DELIMITER = '/';

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match(sprintf('%s%s%s', self::DELIMITER, self::CALLSIGN_REGEX, self::DELIMITER), $value) === 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid VATSIM callsign';
    }
}
