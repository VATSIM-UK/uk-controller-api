<?php

namespace App\Models\Squawk\UnitDiscrete;

use App\Caster\UnitDiscreteSquawkRangeRuleCaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitDiscreteSquawkRangeRule extends Model
{
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'unit_discrete_squawk_range_id',
        'rule',
    ];

    protected $casts = [
        'rule' => UnitDiscreteSquawkRangeRuleCaster::class,
    ];
}
