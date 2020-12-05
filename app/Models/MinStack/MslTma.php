<?php

namespace App\Models\MinStack;

use Illuminate\Database\Eloquent\Model;

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
}
