<?php

namespace App\Http\Livewire\Stand;

use App\Models\Stand\StandReservationPlan;
use App\Models\User\RoleKeys;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RecentlyProcessedPlansTable extends Component implements HasTable
{
    use InteractsWithTable;

    public function makeFilamentTranslatableContentDriver(): ?\Filament\Support\Contracts\TranslatableContentDriver
    {
        return null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->recentlyProcessedPlansQuery())
            ->defaultSort('updated_at', 'desc')
            ->paginated([10])
            ->columns([
                TextColumn::make('name')
                    ->label('Event / Organisation')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'denied' => 'danger',
                        'expired' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('contact_email')
                    ->label('Contact')
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime(),
                TextColumn::make('payload_window')
                    ->label('Window')
                    ->state(fn (StandReservationPlan $record): string => $this->allocationWindowLabel($record)),
                TextColumn::make('requested_stands')
                    ->label('Requested stands')
                    ->state(fn (StandReservationPlan $record): string => $this->requestedStandsLabel($record)),
                TextColumn::make('imported_reservations')
                    ->label('Imported')
                    ->numeric(),
            ])
            ->emptyStateHeading('No processed plans yet.');
    }

    public function render()
    {
        return view('livewire.stand.recently-processed-plans-table');
    }

    private function recentlyProcessedPlansQuery(): Builder
    {
        $query = StandReservationPlan::query()
            ->whereIn('status', ['approved', 'denied', 'expired']);

        if (!$this->userCanViewAll()) {
            $query->where('submitted_by', Auth::id());
        }

        return $query;
    }

    private function allocationWindowLabel(StandReservationPlan $plan): string
    {
        $payload = $plan->payload ?? [];

        $start = $payload['event_start'] ?? $payload['start'] ?? null;
        $end = $payload['event_finish'] ?? $payload['end'] ?? null;

        if ($start === null && $end === null) {
            return 'Per-reservation timing';
        }

        if ($start !== null && $end !== null) {
            return sprintf('%s → %s', $start, $end);
        }

        return sprintf('%s → %s', $start ?? 'Unspecified', $end ?? 'Unspecified');
    }

    private function requestedStandsLabel(StandReservationPlan $plan): string
    {
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

    private function standLabel(array $standData): string
    {
        $airport = $standData['airport'] ?? 'Unknown';
        $stand = $standData['stand'] ?? 'Unknown';

        return sprintf('%s %s', $airport, $stand);
    }

    private function userCanViewAll(): bool
    {
        return Auth::user()->roles()
            ->whereIn('key', [
                RoleKeys::WEB_TEAM,
                RoleKeys::OPERATIONS_TEAM,
                RoleKeys::DIVISION_STAFF_GROUP,
            ])
            ->exists();
    }
}
