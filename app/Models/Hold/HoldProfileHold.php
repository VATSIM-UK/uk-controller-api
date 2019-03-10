<?php
namespace App\Models\Hold;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    /**
     * @return HasOne
     */
    public function hold() : HasOne
    {
        return $this->hasOne(Hold::class);
    }

    /**
     * @return BelongsTo
     */
    public function profile() : BelongsTo
    {
        return $this->belongsTo(HoldProfile::class);
    }
}
