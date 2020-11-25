<?php

namespace App\Models\Aircraft;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aircraft extends Model
{
    public $table = 'aircraft';

    protected $fillable = [
        'code',
        'wingspan',
        'length',
        'wake_category_id',
        'allocate_stands',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'wingspan' => 'double',
        'length' => 'double',
    ];

    public function wakeCategory() : BelongsTo
    {
        return $this->belongsTo(WakeCategory::class);
    }
}
