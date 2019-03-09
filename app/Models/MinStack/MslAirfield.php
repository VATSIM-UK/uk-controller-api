<?php

namespace App\Models\MinStack;

use App\Models\Airfield;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MslAirfield extends Model
{
    public $timestamps = false;

    protected $table = 'msl_airfield';

    protected $fillable = [
        'airfield_id',
        'msl',
        'generated_at',
    ];

    /**
     * @return BelongsTo
     */
    public function airfield() : BelongsTo
    {
        return $this->belongsTo(Airfield::class);
    }
}
