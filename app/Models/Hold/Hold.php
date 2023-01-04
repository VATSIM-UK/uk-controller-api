<?php

namespace App\Models\Hold;

use App\Models\Measurement\MeasurementUnit;
use App\Models\Navigation\Navaid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $fillable = [
        'navaid_id',
        'inbound_heading',
        'minimum_altitude',
        'maximum_altitude',
        'turn_direction',
        'outbound_leg_value',
        'outbound_leg_unit',
        'description',
    ];

    protected $casts = [
        'inbound_heading' => 'integer',
        'minimum_altitude' => 'integer',
        'maximum_altitude' => 'integer',
        'outbound_leg_value' => 'float',
    ];

    public function navaid(): BelongsTo
    {
        return $this->belongsTo(Navaid::class);
    }

    public function restrictions(): HasMany
    {
        return $this->hasMany(HoldRestriction::class);
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

    public function outboundLegUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'outbound_leg_unit');
    }
}
