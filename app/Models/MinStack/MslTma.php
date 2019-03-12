<?php

namespace App\Models\MinStack;

use App\Models\Airfield;
use App\Models\Tma;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MslTma extends Model
{
    protected $primaryKey = 'tma_id';

    public $timestamps = false;

    protected $table = 'msl_tma';

    protected $fillable = [
        'tma_id',
        'msl',
        'generated_at',
    ];

    /**
     * @return BelongsTo
     */
    public function tma() : BelongsTo
    {
        return $this->belongsTo(Tma::class);
    }

    /**
     * @return HasOne
     */
    public function airfield() : HasOne
    {
        return $this->hasOne(Airfield::class);
    }
}
