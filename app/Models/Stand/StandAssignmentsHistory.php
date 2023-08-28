<?php

namespace App\Models\Stand;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandAssignmentsHistory extends Model
{
    use SoftDeletes;

    protected $table = 'stand_assignments_history';

    const CREATED_AT = 'assigned_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'callsign',
        'stand_id',
        'user_id',
        'type',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function stand()
    {
        return $this->belongsTo(Stand::class);
    }
}
