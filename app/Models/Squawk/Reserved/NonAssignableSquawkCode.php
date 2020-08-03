<?php

namespace App\Models\Squawk\Reserved;

use Illuminate\Database\Eloquent\Model;

class NonAssignableSquawkCode extends Model
{
    protected $fillable = [
        'code',
        'description',
    ];

    public $timestamps = false;
}
