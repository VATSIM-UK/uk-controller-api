<?php

namespace App\Models\Release\Enroute;

use Illuminate\Database\Eloquent\Model;

class EnrouteReleaseType extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tag_string',
        'description'
    ];
}
