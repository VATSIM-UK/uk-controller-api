<?php
namespace App\Models\Squawks;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Model for a squawk allocation.
 *
 * Class Allocation
 * @package App\Models\Squawks
 */
class Allocation extends Model
{

    protected $table = 'squawk_allocation';

    public $timestamps = false;

    protected $dates = [
        'allocated_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'callsign',
        'squawk',
        'allocated_at',
        'allocated_by',
    ];

    /**
     * Sets allocated time to now
     *
     * @return Allocation
     */
    public function touchAllocated() : Allocation
    {
        $this->allocated_at = Carbon::now();
        $this->save();
        return $this;
    }
}
