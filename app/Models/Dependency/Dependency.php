<?php

namespace App\Models\Dependency;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Dependency extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uri',
        'local_file',
        'updated_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (Dependency $dependency) {
            $dependency->updated_at = Carbon::now();
        });
    }

    public function getDateFormat() : string
    {
        return 'U';
    }
}
