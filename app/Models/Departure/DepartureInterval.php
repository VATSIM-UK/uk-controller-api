<?php

namespace App\Models\Departure;

use App\Models\Sid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DepartureInterval extends Model
{
    protected $fillable = [
        'type_id',
        'interval',
        'expires_at',
    ];

    public $timestamps = [
        'expires_at',
    ];

    public function sids(): BelongsToMany
    {
        return $this->belongsToMany(Sid::class);
    }

    public function scopeActive(Builder $query)
    {
        $query->where('expires_at' > Carbon::now());
    }

    public function expired(): bool
    {
        return $this->expires_at <= Carbon::now();
    }

    public function expire()
    {
        $this->expires_at = Carbon::now();
        $this->save();
        return $this;
    }
}
