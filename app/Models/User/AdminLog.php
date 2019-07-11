<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    public $table = 'admin_log';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'request_uri',
        'request_body',
    ];
}
