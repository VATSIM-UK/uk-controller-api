<?php

namespace App\Models\Squawks;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for a squawk allocation.
 *
 * Class Allocation
 * @package App\Models\Squawks
 */
class AllocationHistory extends Model
{

    protected $table = 'squawk_allocation_history';

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
        'new',
        'allocated_at',
        'allocated_by',
    ];
}
