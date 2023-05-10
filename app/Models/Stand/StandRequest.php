<?php

namespace App\Models\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

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

    public function scopeCurrent(Builder $query): Builder
    {
        return $this->scopeHasNotExpired($query->where('requested_time', '<', Carbon::now()->addMinutes(40)));
    }

    public function scopeHasNotExpired(Builder $query): Builder
    {
        return $query->where('requested_time', '>', Carbon::now()->subMinutes(20));
    }
}
