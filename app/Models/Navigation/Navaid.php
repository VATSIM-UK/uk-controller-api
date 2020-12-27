<?php

namespace App\Models\Navigation;

use Illuminate\Database\Eloquent\Model;

class Navaid extends Model
{
    protected $fillable = [
        'identifier',
        'latitude',
        'longitude',
    ];
}
