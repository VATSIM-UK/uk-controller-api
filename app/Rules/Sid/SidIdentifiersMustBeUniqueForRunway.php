<?php

namespace App\Rules\Sid;

use App\Models\Runway\Runway;
use App\Models\Sid;
use Illuminate\Contracts\Validation\InvokableRule;

class SidIdentifiersMustBeUniqueForRunway implements InvokableRule
{
    private readonly Runway $runway;
    private readonly ?Sid $existingSid;

    public function __construct(Runway $runway, ?Sid $existingSid)
    {
        $this->runway = $runway;
        $this->existingSid = $existingSid;
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
        $clashes = Sid::where('runway_id', $this->runway->id)
            ->where('identifier', $value);

        if ($this->existingSid) {
            $clashes->where('id', '<>', $this->existingSid->id);
        }

        if ($clashes->exists()) {
            $fail('validation.sids.unique_identifier')->translate(
                ['value' => $value, 'airfield' => $this->runway->airfield->code, 'runway' => $this->runway->identifier]
            );
        }
    }
}
