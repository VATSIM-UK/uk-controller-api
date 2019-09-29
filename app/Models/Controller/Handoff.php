<?php

namespace App\Models\Controller;

use App\Models\Sid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Handoff extends Model
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
            'handoff_orders',
            'handoff_id',
            'controller_position_id'
        );
    }

    public function sids() : HasMany
    {
        return $this->hasMany(Sid::class);
    }
}
