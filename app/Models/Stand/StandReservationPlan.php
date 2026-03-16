<?php

namespace App\Models\Stand;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StandReservationPlan extends Model
{
    public const AUTOMATION_DENIED_BY_USER_ID = 1;

    public const AUTOMATION_NOT_APPROVED_REASON = 'Not approved by UKCP Automation';
    public const AUTOMATION_EVENT_STARTED_PRIOR_TO_APPROVAL_REASON = 'Event started prior to approval';

    protected $fillable = [
        'name',
        'contact_email',
        'payload',
        'approval_due_at',
        'approved_at',
        'denied_at',
        'approved_by',
        'denied_by',
        'denied_reason',
        'submitted_by',
        'status',
        'imported_reservations',
    ];

    protected $casts = [
        'payload' => 'array',
        'approval_due_at' => 'datetime',
        'approved_at' => 'datetime',
        'denied_at' => 'datetime',
        'approved_by' => 'integer',
        'denied_by' => 'integer',
        'submitted_by' => 'integer',
        'imported_reservations' => 'integer',
    ];

    public function scopePending(Builder $builder): Builder
    {
        return $builder->where('status', 'pending');
    }

    // Used by review flows to hide requests where the event has already started.
    public function scopePendingWithinApprovalWindow(Builder $builder): Builder
    {
        return $builder
            ->pending()
            ->where(function (Builder $query): void {
                $query
                    ->whereNull("payload->event_start")
                    ->orWhere("payload->event_start", '>=', Carbon::now()->format('Y-m-d H:i:s'));
            });
    }

    public function eventStartAt(): ?Carbon
    {
        // Canonical payloads provide event_start for plan-level activation timing.
        $eventStart = $this->payload['event_start'] ?? null;

        if (!is_string($eventStart) || $eventStart === '') {
            return null;
        }

        try {
            return Carbon::parse($eventStart);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
