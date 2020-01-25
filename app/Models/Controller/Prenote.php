<?php

namespace App\Models\Controller;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Prenote extends Model
{
    protected $fillable = [
        'key',
        'description',
        'created_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function controllers() : BelongsToMany
    {
        return $this->belongsToMany(
            ControllerPosition::class,
            'prenote_orders',
            'prenote_id',
            'controller_position_id'
        )->withPivot('order')
            ->orderBy('order', 'asc');
    }
}
