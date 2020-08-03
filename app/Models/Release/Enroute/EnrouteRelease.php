<?php

namespace App\Models\Release\Enroute;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrouteRelease extends Model
{
    const UPDATED_AT = null;

    const CREATED_AT = 'released_at';

    protected $fillable = [
        'callsign',
        'enroute_release_type_id',
        'initiating_controller',
        'target_controller',
        'release_point',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
