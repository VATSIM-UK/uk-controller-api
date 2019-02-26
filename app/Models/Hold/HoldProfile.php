<?php

namespace App\Models\Hold;

use Illuminate\Database\Eloquent\Model;

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
     * Append to JSON serialisation
     *
     * @var array
     */
    protected $appends = [
        'user_profile'
    ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function holds()
    {
        return $this->hasManyThrough(Hold::class, HoldProfileHold::class, 'hold_id', 'id', 'id', 'hold_profile_id');
    }

    /**
     * Returns whether or not the profile is a user profile
     *
     * @return bool
     */
    public function getUserProfileAttribute() : bool
    {
        return $this->attributes['user_id'] !== null;
    }
}
