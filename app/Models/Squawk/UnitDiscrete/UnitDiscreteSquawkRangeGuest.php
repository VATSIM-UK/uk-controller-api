<?php

namespace App\Models\Squawk\UnitDiscrete;

use Illuminate\Database\Eloquent\Model;

class UnitDiscreteSquawkRangeGuest extends Model
{
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'primary_unit',
        'guest_unit',
    ];
}
