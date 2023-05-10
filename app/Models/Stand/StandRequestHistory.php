<?php

namespace App\Models\Stand;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class StandRequestHistory extends Model
{
    protected $table = 'stand_request_history';

    use MassPrunable;

    protected $fillable = [
        'user_id',
        'stand_id',
        'callsign',
        'requested_time',
        'deleted_at',
    ];

    protected $casts = [
        'requested_time' => 'datetime',
    ];

    public function prunable(): Builder
    {
        return static::where('created_at', '<', Carbon::now()->subMonth());
    }
}
