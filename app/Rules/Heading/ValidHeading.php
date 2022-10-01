<?php


namespace App\Rules\Heading;

use Illuminate\Contracts\Validation\Rule;

class ValidHeading implements Rule
{
    private const HEADING_REGEX = '/^\d{1,3}$/';

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match(self::HEADING_REGEX, (string)$value) === 1 &&
            (int)$value >= 0 && (int)$value <= 360;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.heading');
    }
}
