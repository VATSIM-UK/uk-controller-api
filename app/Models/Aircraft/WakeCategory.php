<?php

namespace App\Models\Aircraft;

use Illuminate\Database\Eloquent\Model;

class WakeCategory extends Model
{
    protected $fillable = [
        'code',
        'description',
        'created_at'
    ];
}
