<?php

namespace App\Filament\Pages;

use App\Models\Stand\StandReservationPlan;
use App\Models\User\RoleKeys;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class StandReservationPlanSubmission extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static ?string $navigationLabel = 'Submit Stand Plan';
    protected static ?string $navigationGroup = 'Airfield';
    protected static ?string $slug = 'stand-reservation-plan-submission';
    protected static string $view = 'filament.pages.stand-reservation-plan-submission';

    public string $name = '';
    public string $contactEmail = '';
    public string $planJson = '';

    public static function shouldRegisterNavigation(): bool
    {
        return self::userCanAccess();
    }

    public function mount(): void
    {
        abort_unless(self::userCanAccess(), 403);
    }

    public function submitPlan(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'contactEmail' => ['required', 'email'],
            'planJson' => ['required', 'string'],
        ]);

        $payload = json_decode($validated['planJson'], true);
        if (!is_array($payload) || !isset($payload['reservations']) || !is_array($payload['reservations'])) {
            $this->addError('planJson', 'Plan JSON must contain a reservations array.');
            return;
        }

        StandReservationPlan::create([
            'name' => $validated['name'],
            'contact_email' => $validated['contactEmail'],
            'payload' => $payload,
            'approval_due_at' => Carbon::now()->addDays(7),
            'status' => 'pending',
            'submitted_by' => Auth::id(),
        ]);

        $this->reset(['name', 'contactEmail', 'planJson']);

        Notification::make()
            ->title('Plan submitted for admin approval')
            ->success()
            ->send();
    }

    private static function userCanAccess(): bool
    {
        return Auth::user()->roles()
            ->whereIn('key', [
                RoleKeys::VAA,
                RoleKeys::WEB_TEAM,
                RoleKeys::OPERATIONS_TEAM,
                RoleKeys::DIVISION_STAFF_GROUP,
            ])->exists();
    }
}
