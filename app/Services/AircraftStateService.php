<?php

namespace App\Services;

use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AircraftStateService
{
    private const FIELDS = ['clearance_flag', 'ground_state'];

    // Aircraft at the defaults are omitted - the plugin reads absence as "no state".
    public function allStates(): Collection
    {
        return NetworkAircraft::where('clearance_flag', true)
            ->orWhereNotNull('ground_state')
            ->get()
            ->map(
                fn (NetworkAircraft $aircraft): array => [
                    'callsign' => $aircraft->callsign,
                    'clearance_flag' => $aircraft->clearance_flag,
                    'ground_state' => $aircraft->ground_state?->value,
                ]
            )
            ->values();
    }

    public function updateStates(array $updates): void
    {
        $aircraft = NetworkAircraft::whereIn('callsign', array_column($updates, 'callsign'))
            ->get()
            ->keyBy('callsign');

        foreach ($updates as $update) {
            $this->updateState($aircraft->get($update['callsign']), $update);
        }
    }

    private function updateState(?NetworkAircraft $aircraft, array $changes): void
    {
        if ($aircraft === null) {
            NetworkAircraftService::createPlaceholderAircraft($changes['callsign']);
            $aircraft = NetworkAircraft::findOrFail($changes['callsign']);
        }

        foreach (self::FIELDS as $field) {
            if (! array_key_exists($field, $changes)) {
                continue;
            }

            // The plugin sends no offset.
            $changedAt = Carbon::parse($changes[$field.'_at'], 'UTC');
            $appliedAt = $aircraft->{$field.'_updated_at'};

            if ($appliedAt !== null && $appliedAt->greaterThanOrEqualTo($changedAt)) {
                continue;
            }

            $aircraft->{$field} = $changes[$field];
            $aircraft->{$field.'_updated_at'} = $changedAt;
        }

        // Values only - every client reports the same change with a different timestamp.
        if (! $aircraft->isDirty(self::FIELDS)) {
            return;
        }

        $aircraft->save();
    }
}
