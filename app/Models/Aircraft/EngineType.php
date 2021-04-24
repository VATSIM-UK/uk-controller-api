<?php

namespace App\Models\Aircraft;

use Illuminate\Database\Eloquent\Model;

class EngineType extends Model
{
    protected $fillable = [
        'type',
        'euroscope_type',
    ];
}
