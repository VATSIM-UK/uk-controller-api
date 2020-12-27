<?php

namespace App\Models\Squawk;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class SquawkAssignmentsHistory extends Model
{
    use SoftDeletes;

    protected $table = 'squawk_assignments_history';

    const CREATED_AT = 'allocated_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'callsign',
        'code',
        'type',
        'user_id'
    ];
}
