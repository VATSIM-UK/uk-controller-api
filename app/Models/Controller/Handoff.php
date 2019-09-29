<?php

namespace App\Models\Controller;

use Illuminate\Database\Eloquent\Model;

class Handoff extends Model
{
    protected $fillable = [
        'key',
        'description',
        'created_at',
    ];
}
