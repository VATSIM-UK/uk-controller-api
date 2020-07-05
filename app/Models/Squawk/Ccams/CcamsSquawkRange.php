<?php

namespace App\Models\Squawk\Ccams;

use App\Models\Squawk\AbstractSquawkRange;

class CcamsSquawkRange extends AbstractSquawkRange
{
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
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
