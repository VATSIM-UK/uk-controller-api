<?php

namespace App\Models\Hold;

use App\Models\Navigation\Navaid;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignedHold extends Model
{
    protected $primaryKey = 'callsign';

    protected $fillable = [
        'callsign',
        'navaid_id',
    ];

    public function networkAircraft(): HasMany
    {
        return $this->hasMany(NetworkAircraft::class, 'callsign', 'callsign');
    }

    public function navaid(): BelongsTo
    {
        return $this->belongsTo(Navaid::class);
    }
}
