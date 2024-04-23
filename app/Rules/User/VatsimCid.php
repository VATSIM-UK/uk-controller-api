<?php

namespace App\Rules\User;

use App\Helpers\Vatsim\VatsimCidValidator;
use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class VatsimCid implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_integer($value)) {
            $fail('CID is not an integer');
            return;
        }

        if (!VatsimCidValidator::isValid($value)) {
            $fail('CID is invalid');
        }
    }
}
