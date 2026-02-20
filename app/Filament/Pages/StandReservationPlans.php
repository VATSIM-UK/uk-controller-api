<?php

namespace App\Filament\Pages;

use App\Imports\Stand\StandReservationsImport;
use App\Models\Stand\StandReservationPlan;
use App\Models\User\RoleKeys;
use Carbon\Carbon;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class StandReservationPlans extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Event Stand Planning';
    protected static ?string $navigationGroup = 'Airfield';
    protected static ?string $slug = 'stand-reservation-plans';
    protected static string $view = 'filament.pages.stand-reservation-plans';

    public ?array $data = [];

    public function mount(): void
    {
        abort_unless(self::userCanAccess(), 403);
        $this->form->fill();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::userCanAccess();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Event / Organisation')
                    ->required()
                    ->maxLength(255),
                TextInput::make('contactEmail')
                    ->label('Contact Email')
                    ->email()
                    ->required(),
                Textarea::make('planJson')
                    ->label('Reservation payload (JSON)')
                    ->required()
                    ->rows(14)
                    ->helperText('Use an object containing a reservations array. Optional top-level start/end (or active_from/active_to) values are used as defaults for each reservation row.'),
            ])
            ->statePath('data');
    }

    public function submitPlan(): void
    {
        $validated = $this->form->getState();

        $payload = json_decode($validated['planJson'], true);
        if (!is_array($payload) || !isset($payload['reservations']) || !is_array($payload['reservations'])) {
            $this->addError('data.planJson', 'Plan JSON must contain a reservations array.');
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

        $this->form->fill();
        $this->resetTable();

        Notification::make()
            ->title('Plan submitted for admin approval')
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->plansQuery())
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('contact_email')->label('Contact')->searchable(),
                TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'approved' => 'success',
                    'denied' => 'danger',
                    'expired' => 'warning',
                    default => 'gray',
                }),
                TextColumn::make('created_at')->label('Submitted')->dateTime()->sortable(),
                TextColumn::make('approval_due_at')->label('Approval due')->dateTime()->sortable(),
                TextColumn::make('payload_window')
                    ->label('Planned window')
                    ->state(fn (StandReservationPlan $record): string => $this->allocationWindowLabel($record)),
                TextColumn::make('requested_stands')
                    ->label('Requested stands')
                    ->state(fn (StandReservationPlan $record): string => $this->requestedStandsLabel($record))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('approved_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('denied_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('imported_reservations')->numeric()->label('Imported')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'denied' => 'Denied',
                        'expired' => 'Expired',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (StandReservationPlan $record): bool => $this->userCanReview() && $record->status === 'pending')
                    ->action(fn (StandReservationPlan $record) => $this->approvePlan($record)),
                Action::make('deny')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (StandReservationPlan $record): bool => $this->userCanReview() && $record->status === 'pending')
                    ->action(fn (StandReservationPlan $record) => $this->denyPlan($record)),
            ]);
    }

    private function approvePlan(StandReservationPlan $plan): void
    {
        if ($plan->status !== 'pending') {
            Notification::make()->title('Plan is no longer pending')->warning()->send();
            return;
        }

        if ($plan->approval_due_at->isPast()) {
            $plan->update(['status' => 'expired']);
            Notification::make()->title('Approval window has expired')->danger()->send();
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
        $this->resetTable();
    }

    private function denyPlan(StandReservationPlan $plan): void
    {
        if ($plan->status !== 'pending') {
            Notification::make()->title('Plan is no longer pending')->warning()->send();
            return;
        }

        $plan->update([
            'status' => 'denied',
            'denied_at' => Carbon::now(),
            'denied_by' => Auth::id(),
        ]);

        Notification::make()->title('Plan denied')->success()->send();
        $this->resetTable();
    }



    public function allocationWindowLabel(StandReservationPlan $plan): string
    {
        $payload = $plan->payload ?? [];

        $start = $payload['start'] ?? $payload['active_from'] ?? null;
        $end = $payload['end'] ?? $payload['active_to'] ?? null;

        if ($start === null && $end === null) {
            return 'Per-reservation timing';
        }

        if ($start !== null && $end !== null) {
            return sprintf('%s → %s', $start, $end);
        }

        return sprintf('%s → %s', $start ?? 'Unspecified', $end ?? 'Unspecified');
    }

    public function requestedStandsLabel(StandReservationPlan $plan): string
    {
        $reservations = collect($plan->payload['reservations'] ?? []);

        if ($reservations->isEmpty()) {
            return 'No reservations';
        }

        return $reservations
            ->map(function (array $reservation): string {
                $airfield = $reservation['airfield'] ?? $reservation['airport'] ?? 'Unknown';
                $stand = $reservation['stand'] ?? 'Unknown';

                return sprintf('%s %s', $airfield, $stand);
            })
            ->take(5)
            ->implode(', ')
            . ($reservations->count() > 5 ? '…' : '');
    }

    public function recentlyProcessedPlans(): Collection
    {
        $query = StandReservationPlan::query()
            ->whereIn('status', ['approved', 'denied', 'expired'])
            ->orderByDesc('updated_at')
            ->limit(10);

        if (!$this->userCanReview()) {
            $query->where('submitted_by', Auth::id());
        }

        return $query->get();
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

    private function plansQuery(): Builder
    {
        $query = StandReservationPlan::query();

        if (!$this->userCanReview()) {
            $query->where('submitted_by', Auth::id());
        }

        return $query;
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

    private function userCanReview(): bool
    {
        return Auth::user()->roles()
            ->whereIn('key', [
                RoleKeys::WEB_TEAM,
                RoleKeys::OPERATIONS_TEAM,
                RoleKeys::DIVISION_STAFF_GROUP,
            ])->exists();
    }
}
