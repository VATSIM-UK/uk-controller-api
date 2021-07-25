<?php

namespace App\Models\Airfield;

use App\Models\Stand\Stand;
use App\Models\Airline\Airline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Terminal extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'airfield_id',
        'key',
        'description',
    ];

    public function airlines(): BelongsToMany
    {
        return $this->belongsToMany(Airline::class);
    }

    public function stands(): HasMany
    {
        return $this->hasMany(Stand::class);
    }
}
