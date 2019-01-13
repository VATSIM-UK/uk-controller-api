<?php

namespace App\Models\Squawks;

use App\Libraries\SquawkValidator;
use Illuminate\Database\Eloquent\Model;

/**
 * An model representing the owner of a particular squawk code. Due to the relations
 * with SquawkGeneral and SquawkUnit, one of these may pertain to either of the aforementioned
 * models. This model is intended to allow two different methods of squawk allocation to have a common
 * interface with the code ranges themselves.
 *
 * Class SquawkRangeOwner
 * @package App\Models\Squawks
 */
class SquawkRangeOwner extends Model
{

    protected $table = 'squawk_range_owner';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * Each owner may have many squawk ranges that belong to it.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ranges()
    {
        return $this->hasMany(Range::class, "squawk_range_owner_id");
    }
}
