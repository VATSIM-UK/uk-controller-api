<?php

namespace App\Rules\Stand;

use App\Models\Stand\Stand;
use Closure;
use Illuminate\Contracts\Validation\InvokableRule;

class StandIdentifierMustBeUniqueAtAirfield implements InvokableRule
{
    private readonly Closure $get;

    public function __construct(Closure $get)
    {
        $this->get = $get;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        $clashes = Stand::where('airfield_id', ($this->get)('airfield_id'))
            ->where('identifier', $value)
            ->exists();

        if ($clashes) {
            $fail('validation.stand_unique_identifier')->translate(['value' => $value]);
        }
    }
}
