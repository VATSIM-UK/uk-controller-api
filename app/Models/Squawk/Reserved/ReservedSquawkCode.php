<?php

namespace App\Models\Squawk\Reserved;

use Illuminate\Database\Eloquent\Model;

class ReservedSquawkCode extends Model
{
    protected $fillable = [
        'code',
        'description',
    ];

    public $timestamps = false;
}
