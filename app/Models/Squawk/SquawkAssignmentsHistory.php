<?php

namespace App\Models\Squawk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SquawkAssignmentsHistory extends Model
{
    use SoftDeletes;

    protected $table = 'squawk_assignments_history';

    public const CREATED_AT = 'allocated_at';

    public const UPDATED_AT = null;

    protected $fillable = [
        'callsign',
        'code',
        'type',
        'user_id',
    ];
}
