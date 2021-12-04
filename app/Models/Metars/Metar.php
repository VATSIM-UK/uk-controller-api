<?php

namespace App\Models\Metars;

use App\Models\Airfield\Airfield;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Metar extends Model
{
    use HasFactory;

    protected $fillable = [
        'airfield_id',
        'parsed',
        'raw',
    ];

    protected $casts = [
        'airfield_id' => 'integer',
        'parsed' => 'array',
    ];

    public static function boot()
    {
        parent::boot();
        parent::creating(function (Metar $metar) {
            if (!$metar->parsed) {
                $metar->parsed = [];
            }
        });
    }

    public function getQnhAttribute(): ?int
    {
        return $this->parsed['qnh'];
    }

    public function airfield(): BelongsTo
    {
        return $this->belongsTo(Airfield::class);
    }
}
