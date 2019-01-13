<?php

namespace App\Models\Squawks;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents a general squawk range - not specific to any particular
 * airfield, but may be specific to things such as country of origin. E.g.
 * ORCAM and CCAMS.
 *
 * Class General
 * @package App\Models\Squawks
 */
class SquawkGeneral extends Model
{

    protected $table = 'squawk_general';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'squawk_range_owner_id',
        'departure_ident',
        'arrival_ident',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * A given airfield configuration may have many squawk ranges at its disposal.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function getRangesAttribute()
    {
        return $this->rangeOwner->ranges;
    }

    /**
     * A given combination has a single rangeowner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rangeOwner()
    {
        return $this->hasOne(SquawkRangeOwner::class, 'id', 'squawk_range_owner_id');
    }
}
