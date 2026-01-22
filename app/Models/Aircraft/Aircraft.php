<?php

namespace App\Models\Aircraft;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Aircraft extends Model
{
    public $table = 'aircraft';

    protected $fillable = [
        'code',
        'aerodrome_reference_code',
        'wingspan',
        'length',
        'allocate_stands',
        'is_business_aviation',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'wingspan' => 'double',
        'length' => 'double',
    ];

    public function wakeCategories() : BelongsToMany
    {
        return $this->belongsToMany(
            WakeCategory::class,
            'aircraft_wake_category',
        );
    }

    public static function isBusinessAviation($planned_aircraft): bool
    {
        $businessAviationAircraft = [
            'C25A','C25B','C25C','C510','C525','C550','C560','C650','C680',
            'E35L','E55P','E545','E550','E75L',
            'F2TH','F900','F50','F7X','F8X',
            'GLF4','GLF5','GLF6','GLEX',
            'CL30','CL60','BD700',
            'PC12','PC24',
            'LJ35','LJ45','LJ60',
        ];

        return in_array($planned_aircraft, $businessAviationAircraft, true);
    }
}
