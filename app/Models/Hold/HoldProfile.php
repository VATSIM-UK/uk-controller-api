<?php

namespace App\Models\Hold;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model for a hold profile
 *
 * Class HoldProfile
 * @package App\Models\Squawks
 */
class HoldProfile extends Model
{
    protected $table = 'hold_profile';

    public $timestamps = true;

    protected $hidden = [
        'created_at',
        'updated_at',
        'user_id',
        'hold_profile_hold',
    ];

    protected $appends = [
        'holds'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'user_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function getHoldsAttribute()
    {
        return $this->holdProfileHold->pluck('hold_id')->toArray();
    }

    /**
     * @return HasMany
     */
    public function holdProfileHold() : HasMany
    {
        return $this->hasMany(HoldProfileHold::class);
    }

    /**
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
