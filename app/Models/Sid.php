<?php

namespace App\Models;

use App\Models\Controller\Handoff;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sid extends Model
{
    public $timestamps = true;

    public $table = 'sid';

    protected $fillable = [
        'airfield_id',
        'identifier',
        'initial_altitude',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * @return HasOne
     */
    public function airfield() : HasOne
    {
        return $this->hasOne(Airfield::class, 'id', 'airfield_id');
    }

    public function handoff() : HasOne
    {
        return $this->hasOne(Handoff::class);
    }
}
