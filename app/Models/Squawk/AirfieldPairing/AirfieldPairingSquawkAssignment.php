<?php

namespace App\Models\Squawk\AirfieldPairing;

use App\Allocator\Squawk\SquawkAssignmentInterface;
use Illuminate\Database\Eloquent\Model;

class AirfieldPairingSquawkAssignment extends Model implements SquawkAssignmentInterface
{
    protected $primaryKey = 'callsign';

    protected $keyType = 'string';

    const UPDATED_AT = null;

    public $incrementing = false;

    protected $dates = [
        'created_at',
    ];

    protected $fillable = [
        'callsign',
        'code',
    ];

    public function getCode(): string
    {
        return $this->attributes['code'];
    }

    public function getType(): string
    {
        return "AIRFIELD_PAIR";
    }

    public function getCallsign(): string
    {
        return $this->attributes['callsign'];
    }
}
