<?php
namespace App\Models\Hold;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for a hold profile
 *
 * Class HoldProfile
 * @package App\Models\Squawks
 */
class Hold extends Model
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
        'user_id',
        'data',
    ];
}
