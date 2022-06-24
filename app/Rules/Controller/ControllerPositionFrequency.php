<?php

namespace App\Rules\Controller;

use Illuminate\Contracts\Validation\Rule;

class ControllerPositionFrequency implements Rule
{
    const FREQUENCY = '2022'

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        return is_numeric($value) && (((float) $value) % 0.025) === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid controller frequency';
    }
}
