<?php

namespace App\Models\Squawk\Ccams;

use App\Allocator\Squawk\SquawkRangeInterface;
use App\Models\Squawk\AbstractSquawkRange;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
