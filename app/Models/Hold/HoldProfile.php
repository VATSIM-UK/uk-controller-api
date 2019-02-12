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
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function holds()
    {
        return $this->hasMany(HoldProfileHold::class, 'id', 'hold_profile_id');
    }
}
