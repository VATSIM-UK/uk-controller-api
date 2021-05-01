<?php

namespace App\Models\MinStack;

use App\Models\Airfield\Airfield;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MslAirfield extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = 'generated_at';

    protected $primaryKey = 'airfield_id';

    public $timestamps = true;

    protected $table = 'msl_airfield';

    protected $fillable = [
        'airfield_id',
        'msl',
        'generated_at',
    ];

    public function airfield(): BelongsTo
    {
        return $this->belongsTo(Airfield::class);
    }
}
