<?php

namespace App\Models\Acars;

use Illuminate\Database\Eloquent\Model;

class AcarsMessage extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'message',
        'successful',
    ];

    protected $casts = [
        'successful' => 'boolean',
    ];
}
