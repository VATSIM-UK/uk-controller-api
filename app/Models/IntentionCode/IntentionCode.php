<?php

namespace App\Models\IntentionCode;

use Illuminate\Database\Eloquent\Model;

class IntentionCode extends Model
{
    protected $fillable = [
        'code',
        'conditions',
        'priority'
    ];

    protected $casts = [
        'code' => 'array',
        'conditions' => 'array',
        'priority' => 'integer',
    ];
}
