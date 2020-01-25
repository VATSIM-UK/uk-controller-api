<?php

namespace App\Models;

use App\Models\Airfield\Airfield;
use App\Models\Controller\Handoff;
use App\Models\Controller\Prenote;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sid extends Model
{
    public $timestamps = true;

    public $table = 'sid';

    protected $fillable = [
        'airfield_id',
        'identifier',
        'initial_altitude',
        'handoff_id',
        'prenote_id',
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

    public function prenotes() : BelongsToMany
    {
        return $this->belongsToMany(
            Prenote::class,
            'sid_prenotes',
            'sid_id',
            'prenote_id'
        );
    }
}
