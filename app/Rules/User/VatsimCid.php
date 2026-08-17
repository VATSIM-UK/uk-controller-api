<?php

namespace App\Rules\User;

use App\Helpers\Vatsim\VatsimCidValidator;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class VatsimCid implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string):PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_int($value)) {
            $fail('CID is not an integer');

            return;
        }

        if (! VatsimCidValidator::isValid($value)) {
            $fail('CID is invalid');
        }
    }
}
