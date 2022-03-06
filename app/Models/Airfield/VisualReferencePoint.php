<?php

namespace App\Models\Airfield;

use Illuminate\Database\Eloquent\Model;

class VisualReferencePoint extends Model
{
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
    ];
}
