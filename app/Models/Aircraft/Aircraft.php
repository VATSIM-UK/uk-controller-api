<?php

namespace App\Models\Aircraft;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aircraft extends Model
{
    public $table = 'aircraft';

    protected $fillable = [
        'code',
        'wake_category_id',
        'created_at'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function wakeCategory() : BelongsTo
    {
        return $this->belongsTo(WakeCategory::class);
    }
}
