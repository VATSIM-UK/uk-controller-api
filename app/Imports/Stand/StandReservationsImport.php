<?php

namespace App\Imports\Stand;

use App\Models\Stand\Stand;
use App\Models\Stand\StandReservation;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class StandReservationsImport implements ToCollection
{
    use Importable;

    private $stands;

    public function __construct()
    {
        $stands = Stand::with('airfield')->get();
        foreach ($stands as $stand) {
            $this->stands[$stand->airfield->code][$stand->identifier] = $stand->id;
        }
    }

    /**
     * Row format:
     *
     * 0 - Airport ICAO
     * 1 - Stand identifier
     * 2 - Callsign (optional)
     * 3 - Start datetime
     * 4 - End datetime
     *
     * @param Collection[] $rows
     */
    public function collection(Collection $rows)
    {
        $this->output->progressStart($rows->count());
        foreach ($rows as $row) {
            if (!$this->rowValid($row)) {
                $this->output->warning(sprintf('Invalid reservation: %s', implode(', ', $row->toArray())));
                $this->output->progressAdvance();
                continue;
            }

            StandReservation::create(
                [
                    'stand_id' => $this->stands[$row[0]][$row[1]],
                    'callsign' => $row[2] ?? null,
                    'start' => Carbon::parse($row[3]),
                    'end' => Carbon::parse($row[4]),
                ]
            );
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function rowValid(Collection $row): bool
    {
        try {
            return isset($row[0]) &&
                array_key_exists($row[0], $this->stands) &&
                isset($row[1]) &&
                array_key_exists($row[1], $this->stands[$row[0]]) &&
                isset($row[3]) &&
                ($startTime = Carbon::parse($row[3])) &&
                isset($row[4]) &&
                ($endTime = Carbon::parse($row[4])) &&
                $endTime > $startTime;
        } catch (Exception $exception) {
            return false;
        }
    }
}
