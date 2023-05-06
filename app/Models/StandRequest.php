<?php

namespace App\Models;

use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stand_id',
        'callsign',
        'requested_time',
    ];

    protected $casts = [
        'requested_time' => 'datetime',
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
