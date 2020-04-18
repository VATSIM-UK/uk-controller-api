<?php

namespace App\Models\Srd;

use Illuminate\Database\Eloquent\Model;

class SrdNote extends Model
{
    protected $fillable = [
        'id',
        'note_text',
    ];

    protected $casts = [
        'id' => 'integer',
    ];
}
