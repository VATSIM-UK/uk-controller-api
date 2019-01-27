<?php

namespace App\Models\PluginError;

use Illuminate\Database\Eloquent\Model;

class PluginError extends Model
{
    protected $table = 'plugin_error';

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'user_report',
        'data'
    ];
}
