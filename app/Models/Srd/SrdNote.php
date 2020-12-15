<?php

namespace App\Models\Srd;

use Illuminate\Database\Eloquent\Model;

class SrdNote extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'note_text',
    ];

    protected $casts = [
        'id' => 'integer',
    ];
}
