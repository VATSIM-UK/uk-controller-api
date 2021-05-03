<?php

namespace App\Models\Navigation;

use App\Models\Hold\Hold;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Location\Coordinate;

class Navaid extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double'
    ];

    public function holds() : HasMany
    {
        return $this->hasMany(Hold::class);
    }

    public function getRouteKeyName() : string
    {
        return 'identifier';
    }

    public function getCoordinateAttribute(): Coordinate
    {
        return new Coordinate($this->latitude, $this->longitude);
    }
}
