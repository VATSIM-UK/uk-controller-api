<?php

namespace App\Models\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandAssignment extends Model
{
    const UPDATED_AT = null;

    public $incrementing = false;

    public $keyType = 'string';

    public $primaryKey = 'callsign';

    protected $fillable = [
        'callsign',
        'stand_id',
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
