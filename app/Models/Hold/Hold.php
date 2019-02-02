<?php
namespace App\Models\Hold;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for a squawk allocation.
 *
 * Class Allocation
 * @package App\Models\Squawks
 */
class Hold extends Model
{
    protected $table = 'hold';

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
        'fix',
        'inbound_heading',
        'minimum_altitude',
        'maximum_altitude',
        'turn_direction',
        'description',
    ];
}
