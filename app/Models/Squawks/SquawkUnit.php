<?php

namespace App\Models\Squawks;

use App\Libraries\SquawkValidator;
use Illuminate\Database\Eloquent\Model;

/**
 * A squawk range that is specific to a particular airfield or operational
 * unit.
 *
 * Class Local
 * @package App\Models\Squawks
 */
class SquawkUnit extends Model
{
    protected $table = 'squawk_unit';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unit',
        'squawk_range_owner_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * A given ATC Unit may have many squawk ranges at its disposal.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function getRangesAttribute()
    {
        return $this->rangeOwner->ranges;
    }

    /**
     * A given ATC unit has one range owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rangeOwner()
    {
        return $this->hasOne(SquawkRangeOwner::class, 'id', 'squawk_range_owner_id');
    }
}
