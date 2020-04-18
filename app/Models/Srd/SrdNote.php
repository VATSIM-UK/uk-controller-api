<?php

namespace App\Models\Srd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SrdNote extends Model
{
    protected $fillable = [
        'id',
        'note_text',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(SrdRoute::class);
    }
}
