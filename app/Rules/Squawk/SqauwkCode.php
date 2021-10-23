<?php

namespace App\Rules\Squawk;

use Illuminate\Contracts\Validation\Rule;

class SqauwkCode implements Rule
{
    const SQUAWK_REGEX = '/^[0-7]{4}$/';
    const REGEX_MATCHED = 1;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return is_string($value) && preg_match(self::SQUAWK_REGEX, $value) === self::REGEX_MATCHED;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid squawk code';
    }
}
