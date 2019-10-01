<?php

namespace App\Models\Dependency;

use Illuminate\Database\Eloquent\Model;

class Dependency extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uri',
        'local_file',
    ];

    public function getDateFormat() : string
    {
        return 'U';
    }
}
