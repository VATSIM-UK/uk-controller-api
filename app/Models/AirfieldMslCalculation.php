<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AirfieldMslCalculation extends Model
{
    public $timestamps = false;

    protected $table = 'airfield_msl_calculation';

    /**
     * @var array
     */
    protected $fillable = [
        'airfield_id',
        'calculation',
    ];

    /**
     * @return BelongsTo
     */
    public function airfield() : BelongsTo
    {
        return $this->belongsTo(Airfield::class);
    }
}
