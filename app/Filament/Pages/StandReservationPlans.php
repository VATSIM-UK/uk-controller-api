<?php

namespace App\Filament\Pages;

use App\Imports\Stand\StandReservationsImport;
use App\Models\Stand\StandReservationPlan;
use App\Models\User\RoleKeys;
use App\Services\JsonSchema\StandReservationPlanSchemaValidator;
use App\Services\Stand\StandReservationPayloadRowsBuilder;
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
        $this->autoExpirePendingPlansOnOrAfterEventDay();
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
                    ->helperText('Use an object containing either a reservations array or stand_slots array. Top-level event_start/event_finish are treated as defaults for nested reservations. See https://github.com/VATSIM-UK/uk-controller-api/tree/main/docs/schemas/stand-reservation-plan-format.md for the formal specification and https://github.com/VATSIM-UK/uk-controller-api/tree/main/docs/schemas/stand-reservation-plan.schema.json for machine validation.'),
            ])
            ->statePath('data');
    }

    public function submitPlan(): void
    {
        $validated = $this->form->getState();

        // Accept raw JSON from the form and validate its shape before persistence.
        $payload = json_decode($validated['planJson'], true);
        $eventStart = null;
        $planJsonError = null;

        if (!is_array($payload) || (!isset($payload['reservations']) && !isset($payload['stand_slots']))) {
            $planJsonError = 'Plan JSON must contain either reservations or stand_slots.';
        } else {
            // Enforce the canonical schema used by API and documentation.
            $schemaErrors = app(StandReservationPlanSchemaValidator::class)->validatePayload($payload);
            if ($schemaErrors !== []) {
                $planJsonError = 'Plan JSON does not match schema: ' . $schemaErrors[0];
            } else {
                $eventStart = $this->eventStartAt($payload);
                if ($eventStart === null) {
                    $planJsonError = 'Plan JSON must include event_start.';
                } elseif ($eventStart->startOfDay()->lt(Carbon::today())) {
                    $planJsonError = 'Event start must be today or in the future.';
                }
            }
        }

        if ($planJsonError !== null) {
            $this->addError('data.planJson', $planJsonError);
            return;
        }

        StandReservationPlan::create([
            'name' => $validated['name'],
            'contact_email' => $validated['contactEmail'],
            'payload' => $payload,
            'approval_due_at' => $eventStart->copy()->subDay(),
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


    private function eventStartAt(array $payload): ?Carbon
    {
        $eventStart = $payload['event_start'] ?? null;

        if (!is_string($eventStart) || $eventStart === '') {
            return null;
        }

        return Carbon::parse($eventStart);
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

        // Event day is a hard deadline: pending plans are marked expired once that day starts.
        $eventStart = $plan->eventStartAt();
        if ($eventStart !== null && $eventStart->startOfDay()->lte(Carbon::today())) {
            $plan->update([
                'status' => 'expired',
                'denied_at' => null,
                'denied_by' => null,
            ]);

            Notification::make()->title('Plan expired because event day has started')->warning()->send();
            $this->resetTable();

            return;
        }

        // Build normalized row data, then import reservations in one pass.
        $rows = app(StandReservationPayloadRowsBuilder::class)->fromPayload($plan->payload);
        $createdReservations = app(StandReservationsImport::class)->importReservations($rows);

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

        $start = $payload['event_start'] ?? null;
        $end = $payload['event_finish'] ?? null;

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
        // Merge direct reservations and stand slot reservations into one deduplicated label list.
        $reservationStands = collect($plan->payload['reservations'] ?? [])
            ->filter(fn (mixed $reservation): bool => is_array($reservation))
            ->map(fn (array $reservation): string => $this->standLabel($reservation));

        $slotStands = collect($plan->payload['stand_slots'] ?? [])
            ->filter(fn (mixed $slot): bool => is_array($slot))
            ->map(fn (array $slot): string => $this->standLabel($slot));

        $requestedStands = $reservationStands->concat($slotStands)->filter()->unique()->values();

        if ($requestedStands->isEmpty()) {
            return 'No reservations';
        }

        return $requestedStands
            ->take(5)
            ->implode(', ')
            . ($requestedStands->count() > 5 ? '…' : '');
    }



    private function autoExpirePendingPlansOnOrAfterEventDay(): void
    {
        $today = Carbon::today();

        StandReservationPlan::query()
            ->pending()
            ->whereNotNull('payload->event_start')
            ->get()
            ->each(function (StandReservationPlan $plan) use ($today): void {
                $eventStart = $plan->eventStartAt();

                if ($eventStart === null || $eventStart->startOfDay()->gt($today)) {
                    return;
                }

                $plan->update([
                    'status' => 'expired',
                    'denied_at' => null,
                    'denied_by' => null,
                ]);
            });
    }

    private function plansQuery(): Builder
    {
        $query = StandReservationPlan::query();

        // Non-review users should only see plans they submitted.
        if (!$this->userCanViewAll()) {
            $query->where('submitted_by', Auth::id());
        }

        return $query;
    }

    private static function userCanAccess(): bool
    {
        return self::userHasAnyRole([
            RoleKeys::VAA,
            RoleKeys::WEB_TEAM,
            RoleKeys::OPERATIONS_TEAM,
            RoleKeys::DIVISION_STAFF_GROUP,
        ]);
    }

    private function userCanReview(): bool
    {
        return self::userHasAnyRole([
            RoleKeys::OPERATIONS_TEAM,
            RoleKeys::DIVISION_STAFF_GROUP,
        ]);
    }

    private function userCanViewAll(): bool
    {
        return self::userHasAnyRole([
            RoleKeys::WEB_TEAM,
            RoleKeys::OPERATIONS_TEAM,
            RoleKeys::DIVISION_STAFF_GROUP,
        ]);
    }

    private function standLabel(array $standData): string
    {
        $airport = $standData['airport'] ?? 'Unknown';
        $stand = $standData['stand'] ?? 'Unknown';

        return sprintf('%s %s', $airport, $stand);
    }

    private static function userHasAnyRole(array $roleKeys): bool
    {
        return Auth::user()->roles()
            ->whereIn('key', $roleKeys)
            ->exists();
    }
}
