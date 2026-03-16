<?php

namespace App\Models\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandAssignment extends Model
{
    public const SOURCE_USER = 'manual';
    public const SOURCE_RESERVATION_ALLOCATOR = 'reservation_allocator';
    public const SOURCE_VAA_ALLOCATOR = 'vaa_allocator';
    public const SOURCE_SYSTEM = 'system_auto';

    const UPDATED_AT = null;

    public $incrementing = false;

    public $keyType = 'string';

    public $primaryKey = 'callsign';

    protected $fillable = [
        'callsign',
        'stand_id',
        'assignment_source',
    ];

    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }

    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(NetworkAircraft::class, 'callsign', 'callsign');
    }
}
