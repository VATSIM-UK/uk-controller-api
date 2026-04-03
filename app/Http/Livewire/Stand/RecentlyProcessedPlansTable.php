<?php

namespace App\Http\Livewire\Stand;

use App\Models\Stand\StandReservationPlan;
use App\Models\User\RoleKeys;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RecentlyProcessedPlansTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
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
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'denied' => 'Rejected',
                        default => ucfirst($state),
                    })
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
                    ->state(fn (StandReservationPlan $record): string => $this->requestedStandsLabel($record))
                    ->tooltip(fn (StandReservationPlan $record): ?string => $this->requestedStandsTooltip($record)),
                TextColumn::make('denied_reason')
                    ->label('Rejection reason')
                    ->tooltip(fn (StandReservationPlan $record): ?string => $record->denied_reason)
                    ->wrap(),
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
        // Show only completed review outcomes in this historical table.
        $query = StandReservationPlan::query()
            ->whereIn('status', ['approved', 'denied', 'expired']);

        // Match page-level permissions by restricting standard users to their own submissions.
        if (!$this->userCanViewAll()) {
            $query->where('submitted_by', Auth::id());
        }

        return $query;
    }

    private function allocationWindowLabel(StandReservationPlan $plan): string
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

    private function requestedStandsLabel(StandReservationPlan $plan): string
    {
        // Build a concise, deduplicated stand summary across all payload branches.
        $requestedStands = $this->requestedStands($plan);

        if ($requestedStands->isEmpty()) {
            return 'No reservations';
        }

        return $requestedStands
            ->take(5)
            ->implode(', ')
            . ($requestedStands->count() > 5 ? '…' : '');
    }

    private function requestedStandsTooltip(StandReservationPlan $plan): ?string
    {
        $requestedStands = $this->requestedStands($plan);

        if ($requestedStands->isEmpty()) {
            return null;
        }

        return $requestedStands
            ->values()
            ->map(fn (string $stand, int $index): string => sprintf('%d. %s', $index + 1, $stand))
            ->implode(PHP_EOL);
    }

    private function requestedStands(StandReservationPlan $plan): \Illuminate\Support\Collection
    {
        return $this->extractStandLabels($plan->payload['reservations'] ?? [])
            ->concat($this->extractStandLabels($plan->payload['stand_slots'] ?? []))
            ->filter()
            ->unique()
            ->values();
    }

    private function extractStandLabels(array $items): \Illuminate\Support\Collection
    {
        return collect($items)
            ->filter(fn (mixed $item): bool => is_array($item))
            ->map(fn (array $item): string => $this->standLabel($item));
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
