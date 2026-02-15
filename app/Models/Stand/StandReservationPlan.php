<?php

namespace App\Models\Stand;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StandReservationPlan extends Model
{
    protected $fillable = [
        'name',
        'contact_email',
        'payload',
        'approval_due_at',
        'approved_at',
        'approved_by',
        'submitted_by',
        'status',
        'imported_reservations',
    ];

    protected $casts = [
        'payload' => 'array',
        'approval_due_at' => 'datetime',
        'approved_at' => 'datetime',
        'approved_by' => 'integer',
        'submitted_by' => 'integer',
        'imported_reservations' => 'integer',
    ];

    public function scopePending(Builder $builder): Builder
    {
        return $builder->where('status', 'pending');
    }

    public function scopePendingWithinApprovalWindow(Builder $builder): Builder
    {
        return $builder->pending()->where('approval_due_at', '>=', Carbon::now());
    }
}
