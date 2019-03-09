<?php

namespace App\Models\MinStack;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TmaMslCalculation extends Model
{
    public $timestamps = false;

    protected $table = 'tma_msl_calculation';

    /**
     * @var array
     */
    protected $fillable = [
        'tma_id',
        'calculation',
    ];

    /**
     * @return HasOne
     */
    public function tma() : BelongsTo
    {
        return $this->belongsTo(Tma::class);
    }
}
