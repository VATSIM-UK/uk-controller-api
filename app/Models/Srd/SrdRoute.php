<?php

namespace App\Models\Srd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SrdRoute extends Model
{
    protected $fillable = [
        'origin',
        'destination',
        'min_level',
        'max_level',
        'route_segment',
        'sid',
        'star',
    ];

    protected $casts = [
        'min_level' => 'integer',
        'max_level' => 'integer'
    ];

    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(SrdNote::class);
    }
}
