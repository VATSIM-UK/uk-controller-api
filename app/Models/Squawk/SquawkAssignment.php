<?php

namespace App\Models\Squawk;

use App\Allocator\Squawk\SquawkAssignmentInterface;
use Illuminate\Database\Eloquent\Model;

class SquawkAssignment extends Model implements SquawkAssignmentInterface
{
    protected $primaryKey = 'callsign';

    protected $keyType = 'string';

    const UPDATED_AT = null;

    public $incrementing = false;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'callsign',
        'code',
        'assignment_type',
    ];

    public function getCode(): string
    {
        return $this->attributes['code'];
    }

    public function getType(): string
    {
        return $this->assignment_type;
    }

    public function getCallsign(): string
    {
        return $this->attributes['callsign'];
    }
}
