<?php

namespace App\Models\Squawk\UnitDiscrete;

use App\Models\Squawk\AbstractSquawkRange;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitDiscreteSquawkRange extends AbstractSquawkRange
{
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'unit',
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

    public function rules(): HasMany
    {
        return $this->hasMany(UnitDiscreteSquawkRangeRule::class);
    }
}
