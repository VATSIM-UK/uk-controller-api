<?php

namespace App\Models\Dependency;

use Illuminate\Database\Eloquent\Model;

class Dependency extends Model
{
    protected $fillable = [
        'uri',
        'local_file',
        'created_at',
    ];
}
