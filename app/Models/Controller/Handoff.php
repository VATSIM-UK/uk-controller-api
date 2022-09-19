<?php

namespace App\Models\Controller;

use App\Models\Airfield\Airfield;
use App\Models\Sid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Handoff extends Model implements HasControllerHierarchy
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

    public function sids(): HasMany
    {
        return $this->hasMany(Sid::class);
    }

    public function airfield(): HasOne
    {
        return $this->hasOne(Airfield::class);
    }
}
