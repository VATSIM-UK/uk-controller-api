<?php

namespace App\Models\Stand;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandReservationPlan extends Model
{
    protected $fillable = [
        'name',
        'contact_email',
        'payload',
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'status',
        'imported_reservations',
    ];

    protected $casts = [
        'payload' => 'array',
        'submitted_by' => 'integer',
        'submitted_at' => 'datetime',
        'approved_by' => 'integer',
        'approved_at' => 'datetime',
        'status' => StandReservationPlanStatus::class,
        'imported_reservations' => 'array',
    ];

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
