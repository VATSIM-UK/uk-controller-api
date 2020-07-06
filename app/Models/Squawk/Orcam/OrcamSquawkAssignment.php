<?php

namespace App\Models\Squawk\Orcam;

use App\Allocator\Squawk\SquawkAssignmentInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrcamSquawkAssignment extends Model implements SquawkAssignmentInterface
{
    protected $primaryKey = 'callsign';

    protected $keyType = 'string';

    const UPDATED_AT = null;

    protected $dates = [
        'created_at',
    ];

    protected $fillable = [
        'callsign',
        'code',
    ];

    public function squawkRange(): BelongsTo
    {
        return $this->belongsTo(OrcamSquawkRange::class);
    }

    public function getCode(): string
    {
        return $this->attributes['code'];
    }

    public function getType(): string
    {
        return "ORCAM";
    }

    public function getCallsign(): string
    {
        return $this->attributes['callsign'];
    }
}
