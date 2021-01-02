<?php

namespace App\Models\Departure;

use App\Models\Sid;
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
}
