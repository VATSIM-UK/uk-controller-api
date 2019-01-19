<?php

namespace App\Models\Squawks;

use App\Libraries\SquawkValidator;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a squawk range. Specifies the start and stop values for the range.
 *
 * Class General
 * @package App\Models\Squawks
 */
class Range extends Model
{
    protected $table = 'squawk_range';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'squawk_range_owner_id',
        'start',
        'stop',
        'rules',
        'allow_duplicate',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * Each squawk range has one owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function squawkRangeOwner()
    {
        return $this->belongsTo(SquawkRangeOwner::class, 'squawk_range_owner_id');
    }

    /**
     * The range of the range. Does NOT discount invalid, non-octal codes
     *
     * @return integer
     */
    public function getNumberOfPossibilitiesAttribute()
    {
        return ($this->stop - $this->start) + 1;
    }

    /**
     * Get a random code from inside the range
     *
     * @return string
     */
    public function getRandomSquawkAttribute() : string
    {
        $valid_code = false;
        $attempts = 0;
        while (!$valid_code) {
            $attempts++;
            $code = rand($this->start, $this->stop);

            // If the code is less than 1000, prepend zeros.
            if (strlen($code) < 4) {
                $code = str_repeat("0", 4 - strlen($code)) . $code;
            }

            if (SquawkValidator::isValidSquawk($code)) {
                $valid_code = $code;
            }
            if ($attempts > 35) {
                return false;
            }
        }
        return $valid_code;
    }
}
