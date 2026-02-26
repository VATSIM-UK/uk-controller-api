<?php

namespace App\Console\Commands;

use App\Imports\Stand\StandReservationsImport as Importer;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class StandReservationsImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stand-reservations:import {file_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import stands reservations';

    /**
     * Execute the console command.
     *
     * @param Importer $importer
     * @return mixed
     */
    public function handle(Importer $importer)
    {
        $fileName = $this->argument('file_name');

        if (!Storage::disk('imports')->exists($fileName)) {
            $this->error(sprintf('Import file not found: %s', $this->argument('file_name')));
            return 1;
        }

        $this->output->title('Starting stand reservations import');

        if ($this->fileIsJson($fileName)) {
            $payload = json_decode(Storage::disk('imports')->get($fileName), true);

            if (!is_array($payload)) {
                $this->error('Import file is not valid JSON');
                return 1;
            }

            $importer->withOutput($this->output)->collection($this->extractRowsFromJson($payload));
        } else {
            $importer->withOutput($this->output)->import($fileName, 'imports', Excel::CSV);
        }

        $this->output->success('Stand reservations import complete');

        return 0;
    }

    private function fileIsJson(string $fileName): bool
    {
        return str_ends_with(strtolower($fileName), '.json');
    }

    private function extractRowsFromJson(array $payload): Collection
    {
        $defaultStart = $payload['event_start'] ?? $payload['start'] ?? null;
        $defaultEnd = $payload['event_finish'] ?? $payload['end'] ?? null;

        // Backward-compatible flat row payloads (`reservations` or a raw array of rows).
        $reservationRows = collect($payload['reservations'] ?? $payload)
            ->filter(fn (mixed $reservation) => is_array($reservation))
            ->map(function (array $reservation) use ($defaultStart, $defaultEnd) {
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

        // Preferred stand-slot payloads where one stand can contain multiple timed reservations.
        $slotRows = collect($payload['stand_slots'] ?? [])
            ->filter(fn (mixed $standSlot) => is_array($standSlot))
            ->flatMap(function (array $standSlot) use ($defaultStart, $defaultEnd) {
                $slotAirfield = $standSlot['airfield'] ?? $standSlot['airport'] ?? null;
                $slotStand = $standSlot['stand'] ?? null;

                return collect($standSlot['slot_reservations'] ?? [])
                    ->filter(fn (mixed $reservation) => is_array($reservation))
                    ->map(function (array $reservation) use ($slotAirfield, $slotStand, $defaultStart, $defaultEnd) {
                        return collect([
                            'airfield' => $reservation['airfield'] ?? $reservation['airport'] ?? $slotAirfield,
                            'stand' => $reservation['stand'] ?? $slotStand,
                            'callsign' => $reservation['callsign'] ?? null,
                            'cid' => $reservation['cid'] ?? null,
                            'origin' => $reservation['origin'] ?? null,
                            'destination' => $reservation['destination'] ?? null,
                            'start' => $reservation['start'] ?? $defaultStart,
                            'end' => $reservation['end'] ?? $defaultEnd,
                        ]);
                    });
            });

        return $reservationRows->concat($slotRows)->values();
    }
}
