<?php
namespace App\Models\Hold;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for a hold profile
 *
 * Class HoldProfile
 * @package App\Models\Squawks
 */
class HoldProfileHold extends Model
{
    protected $table = 'hold_profile_hold';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hold_profile_id',
        'hold_id',
    ];
}
