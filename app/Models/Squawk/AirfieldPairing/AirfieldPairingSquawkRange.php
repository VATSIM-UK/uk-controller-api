<?php

namespace App\Models\Squawk\AirfieldPairing;

use App\Models\Squawk\AbstractSquawkRange;

class AirfieldPairingSquawkRange extends AbstractSquawkRange
{
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'origin',
        'destination',
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
