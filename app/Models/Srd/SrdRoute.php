<?php

namespace App\Models\Srd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SrdRoute extends Model
{
    protected $fillable = [
        'origin',
        'destination',
        'minimum_level',
        'maximum_level',
        'route_segment',
        'sid',
        'star',
    ];

    protected $casts = [
        'minimum_level' => 'integer',
        'maximum_level' => 'integer'
    ];

    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(SrdNote::class);
    }
}
