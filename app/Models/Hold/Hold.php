<?php
namespace App\Models\Hold;

use App\Models\Hold\Hold;
use App\Models\Navigation\Navaid;
use App\Models\Hold\HoldRestriction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model for a hold
 *
 * Class Hold
 * @package App\Models\Squawks
 */
class Hold extends Model
{
    use HasFactory;

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
        'navaid_id',
        'inbound_heading',
        'minimum_altitude',
        'maximum_altitude',
        'turn_direction',
        'description',
    ];

    public function navaid(): BelongsTo
    {
        return $this->belongsTo(Navaid::class);
    }

    /**
     * Relationship between a hold and its restrictions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function restrictions()
    {
        return $this->hasMany(HoldRestriction::class, 'hold_id', 'id');
    }

    public function deemedSeparatedHolds(): BelongsToMany
    {
        return $this->belongsToMany(
            Hold::class,
            'deemed_separated_holds',
            'first_hold_id',
            'second_hold_id',
        )->withPivot('vsl_insert_distance');
    }
}
