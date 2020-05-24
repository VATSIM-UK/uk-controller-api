<?php

namespace App\Models\Hold;

use App\Models\Navigation\Navaid;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssignedHold extends Model
{
    protected $fillable = [
        'callsign',
        'navaid_id',
    ];

    public function networkAircraft(): BelongsTo
    {
        return $this->belongsTo(NetworkAircraft::class, 'callsign', 'callsign');
    }

    public function navaid(): BelongsTo
    {
        return $this->belongsTo(Navaid::class);
    }
}
