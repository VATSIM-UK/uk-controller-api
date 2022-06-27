<?php

namespace App\Rules\Stand;

use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use Closure;
use Illuminate\Contracts\Validation\InvokableRule;

class StandIdentifierMustBeUniqueAtAirfield implements InvokableRule
{
    private readonly Airfield $airfield;
    private readonly ?Stand $existingStand;

    public function __construct(Airfield $airfield, ?Stand $existingStand)
    {
        $this->airfield = $airfield;
        $this->existingStand = $existingStand;
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        $clashes = Stand::where('airfield_id', $this->airfield->id)
            ->where('identifier', $value);

        if ($this->existingStand) {
            $clashes->where('id', '<>', $this->existingStand->id);
        }

        if ($clashes->exists()) {
            $fail('validation.stands.unique_identifier')->translate(
                ['value' => $value, 'airfield' => $this->airfield->code]
            );
        }
    }
}
