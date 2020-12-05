<?php

namespace App\Models\MinStack;

use Illuminate\Database\Eloquent\Model;

class MslAirfield extends Model
{
    protected $primaryKey = 'airfield_id';

    public $timestamps = false;

    protected $table = 'msl_airfield';

    protected $fillable = [
        'airfield_id',
        'msl',
        'generated_at',
    ];
}
