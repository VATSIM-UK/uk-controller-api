<?php

namespace App\Models\Controller;

use Illuminate\Database\Eloquent\Model;

class Prenote extends Model
{
    protected $fillable = [
        'key',
        'description',
        'created_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
