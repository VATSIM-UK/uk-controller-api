<?php

namespace App\Models\Airfield;

use Illuminate\Database\Eloquent\Model;

class Terminal extends Model
{
    protected $fillable = [
        'airfield_id',
        'key',
        'description',
    ];
}
