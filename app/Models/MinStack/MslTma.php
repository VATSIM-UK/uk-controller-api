<?php

namespace App\Models\MinStack;

use App\Models\Tma;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MslTma extends Model
{
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
    public function airfield() : BelongsTo
    {
        return $this->belongsTo(Tma::class);
    }
}
