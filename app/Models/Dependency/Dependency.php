<?php

namespace App\Models\Dependency;

use Illuminate\Database\Eloquent\Model;

class Dependency extends Model
{
    protected $fillable = [
        'key',
        'local_file',
        'created_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
