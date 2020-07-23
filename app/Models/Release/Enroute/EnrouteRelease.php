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
        'initiating_controller',
        'receiving_controller',
        'user_id',
    ];

    /**
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
