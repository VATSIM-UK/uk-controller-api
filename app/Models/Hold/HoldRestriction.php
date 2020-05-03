<?php
namespace App\Models\Hold;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for a restriction on a hold
 *
 * Class HoldRestriction
 * @package App\Models\Hold
 */
class HoldRestriction extends Model
{
    public $timestamps = true;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'restriction' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hold_id',
        'restriction',
    ];
}
