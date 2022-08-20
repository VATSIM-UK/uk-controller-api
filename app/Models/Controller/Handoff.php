<?php

namespace App\Models\Controller;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Handoff extends Model
{
    protected $fillable = [
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
            'handoff_orders',
            'handoff_id',
            'controller_position_id'
        )
            ->orderByPivot('order')
            ->withPivot('order')
            ->withTimestamps();
    }
}
