<?php

namespace App\Models\Navigation;

use App\Models\Hold\Hold;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Navaid extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'identifier',
        'latitude',
        'longitude',
    ];

    public function holds() : HasMany
    {
        return $this->hasMany(Hold::class);
    }
    
    public function getRouteKeyName() : string
    {
        return 'identifier';
    }
}
