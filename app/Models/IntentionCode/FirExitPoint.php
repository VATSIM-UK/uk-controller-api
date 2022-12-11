<?php

namespace App\Models\IntentionCode;

use Illuminate\Database\Eloquent\Model;

class FirExitPoint extends Model
{
    protected $fillable = [
        'exit_point',
        'internal',
        'exit_direction_start',
        'exit_direction_end',
    ];
}
