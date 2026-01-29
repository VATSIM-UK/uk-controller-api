<?php

namespace App\Models\Aircraft;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Aircraft extends Model
{
    public $table = 'aircraft';

    protected $fillable = [
        'code',
        'aerodrome_reference_code',
        'wingspan',
        'length',
        'allocate_stands',
        'is_business_aviation',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'wingspan' => 'double',
        'length' => 'double',
    ];

    public function wakeCategories() : BelongsToMany
    {
        return $this->belongsToMany(
            WakeCategory::class,
            'aircraft_wake_category',
        );
    }
}
