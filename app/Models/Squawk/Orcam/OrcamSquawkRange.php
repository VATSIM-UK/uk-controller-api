<?php

namespace App\Models\Squawk\Orcam;

use App\Models\Squawk\AbstractSquawkRange;

class OrcamSquawkRange extends AbstractSquawkRange
{
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'origin',
        'first',
        'last',
    ];

    public function first(): string
    {
        return $this->attributes['first'];
    }

    public function last(): string
    {
        return $this->attributes['last'];
    }
}
