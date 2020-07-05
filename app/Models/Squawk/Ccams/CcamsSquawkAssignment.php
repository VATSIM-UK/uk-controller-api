<?php

namespace App\Models\Squawk\Ccams;

use App\Allocator\Squawk\SquawkAssignmentInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CcamsSquawkAssignment extends Model implements SquawkAssignmentInterface
{
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'callsign',
        'code',
    ];

    public function squawkRange(): BelongsTo
    {
        return $this->belongsTo(CcamsSquawkRange::class);
    }

    public function getCode(): string
    {
        return $this->attributes['code'];
    }

    public function getType(): string
    {
        return "CCAMS";
    }
}
