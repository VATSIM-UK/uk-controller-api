<?php

namespace App\Models\Controller;

use App\Models\Sid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Prenote extends Model implements HasControllerHierarchy
{
    use HasFactory;

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
        )
            ->orderByPivot('order')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function sids(): BelongsToMany
    {
        return $this->belongsToMany(
            Sid::class,
            'sid_prenotes',
            'prenote_id',
            'sid_id'
        );
    }
}
