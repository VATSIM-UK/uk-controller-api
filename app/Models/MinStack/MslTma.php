<?php

namespace App\Models\MinStack;

use App\Models\Tma;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MslTma extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = 'generated_at';

    protected $primaryKey = 'tma_id';

    public $timestamps = true;

    protected $table = 'msl_tma';

    protected $fillable = [
        'tma_id',
        'msl',
        'generated_at',
    ];

    public function tma(): BelongsTo
    {
        return $this->belongsTo(Tma::class);
    }
}
