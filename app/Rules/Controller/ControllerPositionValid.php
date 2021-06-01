<?php

namespace App\Rules\Controller;

use App\Models\Controller\ControllerPosition;
use Illuminate\Contracts\Validation\Rule;

class ControllerPositionValid implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return ControllerPosition::where('id', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid controller position';
    }
}
