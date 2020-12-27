<?php

namespace App\Models\Aircraft;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WakeCategory extends Model
{
    protected $fillable = [
        'code',
        'description',
        'relative_weighting',
        'created_at'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'relative_weighting' => 'integer',
    ];

    public function scopeGreaterRelativeWeighting(Builder $builder, WakeCategory $wakeCategory): Builder
    {
        return $builder->where('relative_weighting', '>=', $wakeCategory->relative_weighting);
    }
}
