<?php

namespace App\Filament\Pages;

use App\Imports\Stand\StandReservationsImport;
use App\Models\Stand\StandReservationPlan;
use App\Models\User\RoleKeys;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class StandReservationPlanReview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Review Stand Plans';
    protected static ?string $navigationGroup = 'Airfield';
    protected static ?string $slug = 'stand-reservation-plan-review';
    protected static string $view = 'filament.pages.stand-reservation-plan-review';

    public Collection $plans;

    public static function shouldRegisterNavigation(): bool
    {
        return self::userCanAccess();
    }

    public function mount(): void
    {
        abort_unless(self::userCanAccess(), 403);
        $this->refreshPlans();
    }

    public function approvePlan(int $planId): void
    {
        $plan = StandReservationPlan::findOrFail($planId);

        if ($plan->status !== 'pending') {
            Notification::make()->title('Plan is no longer pending')->warning()->send();
            $this->refreshPlans();
            return;
        }

        if ($plan->approval_due_at->isPast()) {
            $plan->update(['status' => 'expired']);
            Notification::make()->title('Approval window has expired')->danger()->send();
            $this->refreshPlans();
            return;
        }

        $createdReservations = app(StandReservationsImport::class)->importReservations($this->rowsFromPayload($plan->payload));

        $plan->update([
            'status' => 'approved',
            'approved_at' => Carbon::now(),
            'approved_by' => Auth::id(),
            'imported_reservations' => $createdReservations,
        ]);

        Notification::make()->title('Plan approved')->success()->send();
        $this->refreshPlans();
    }

    public function denyPlan(int $planId): void
    {
        $plan = StandReservationPlan::findOrFail($planId);
        if ($plan->status !== 'pending') {
            Notification::make()->title('Plan is no longer pending')->warning()->send();
            $this->refreshPlans();
            return;
        }

        $plan->update([
            'status' => 'denied',
            'denied_at' => Carbon::now(),
            'denied_by' => Auth::id(),
        ]);

        Notification::make()->title('Plan denied')->success()->send();
        $this->refreshPlans();
    }

    private function rowsFromPayload(array $payload): Collection
    {
        $defaultStart = $payload['start'] ?? $payload['active_from'] ?? null;
        $defaultEnd = $payload['end'] ?? $payload['active_to'] ?? null;

        return collect($payload['reservations'] ?? [])->map(function (array $reservation) use ($defaultStart, $defaultEnd) {
            return collect([
                'airfield' => $reservation['airfield'] ?? $reservation['airport'] ?? null,
                'stand' => $reservation['stand'] ?? null,
                'callsign' => $reservation['callsign'] ?? null,
                'cid' => $reservation['cid'] ?? null,
                'origin' => $reservation['origin'] ?? null,
                'destination' => $reservation['destination'] ?? null,
                'start' => $reservation['start'] ?? $defaultStart,
                'end' => $reservation['end'] ?? $defaultEnd,
            ]);
        });
    }

    private function refreshPlans(): void
    {
        $this->plans = StandReservationPlan::pending()
            ->orderBy('created_at')
            ->get();
    }

    private static function userCanAccess(): bool
    {
        return Auth::user()->roles()
            ->whereIn('key', [
                RoleKeys::WEB_TEAM,
                RoleKeys::OPERATIONS_TEAM,
                RoleKeys::DIVISION_STAFF_GROUP,
            ])->exists();
    }
}
