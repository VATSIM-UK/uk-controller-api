<?php

namespace App\Models\Release\Departure;

use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DepartureReleaseRequest extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'callsign',
        'user_id',
        'controller_position_id',
        'expires_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'expires_at',
    ];

    public function controllerPositions(): BelongsToMany
    {
        return $this->belongsToMany(ControllerPosition::class)
            ->withPivot('released_at', 'release_expires_at', 'rejected_at');
    }
}
