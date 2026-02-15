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

    private const INDEXED_AIRFIELD = 0;
    private const INDEXED_STAND = 1;
    private const INDEXED_CALLSIGN = 2;
    private const INDEXED_START = 3;
    private const INDEXED_END = 4;
    private const INDEXED_CID = 5;
    private const INDEXED_ORIGIN = 6;
    private const INDEXED_DESTINATION = 7;

    /**
     * Supported row format:
     *
     * Indexed (CSV):
     * 0 - Airport ICAO
     * 1 - Stand identifier
     * 2 - Callsign (optional)
     * 3 - Start datetime
     * 4 - End datetime
     * 5 - CID (optional)
     * 6 - Origin (optional)
     * 7 - Destination (optional)
     *
     * Associative (JSON):
     * airfield, stand, start, end (required)
     * callsign, cid, origin, destination (optional)
     *
     * @param Collection[] $rows
     */
    public function collection(Collection $rows)
    {
        $this->importReservations($rows);
    }

    public function importReservations(Collection $rows): int
    {
        $createdReservations = 0;
        if (isset($this->output)) {
            $this->output->progressStart($rows->count());
        }

        foreach ($rows as $row) {
            $reservationData = $this->extractReservationData($row);

            if (!$reservationData || !$this->rowValid($reservationData)) {
                if (isset($this->output)) {
                    $this->output->warning(sprintf('Invalid reservation: %s', implode(', ', $row->toArray())));
                    $this->output->progressAdvance();
                }
                continue;
            }

            StandReservation::create(
                [
                    'stand_id' => $this->stands[$reservationData['airfield']][$reservationData['stand']],
                    'callsign' => $reservationData['callsign'],
                    'cid' => $reservationData['cid'],
                    'origin' => $reservationData['origin'],
                    'destination' => $reservationData['destination'],
                    'start' => Carbon::parse($reservationData['start']),
                    'end' => Carbon::parse($reservationData['end']),
                ]
            );
            $createdReservations++;
            if (isset($this->output)) {
                $this->output->progressAdvance();
            }
        }

        if (isset($this->output)) {
            $this->output->progressFinish();
        }

        return $createdReservations;
    }

    /**
     * For the data to be valid:
     *
     * - Index 0 must be an airfield ICAO where stands are present (see constructor)
     * - Index 1 must be a valid stand identifier at the airfield in index 0
     * - Index 3 must be a valid timestamp - start time
     * - Index 4 must be a valid timestamp after index 3 - end time
     */
    private function extractReservationData(Collection $row): ?array
    {
        if ($row->has('airfield') || $row->has('stand')) {
            return [
                'airfield' => $row->get('airfield'),
                'stand' => $row->get('stand'),
                'callsign' => $this->emptyStringToNull($row->get('callsign')),
                'cid' => $this->emptyStringToNull($row->get('cid')),
                'origin' => $this->emptyStringToNull($row->get('origin')),
                'destination' => $this->emptyStringToNull($row->get('destination')),
                'start' => $row->get('start'),
                'end' => $row->get('end'),
            ];
        }

        if (!$row->has(self::INDEXED_AIRFIELD) || !$row->has(self::INDEXED_STAND)) {
            return null;
        }

        return [
            'airfield' => $row->get(self::INDEXED_AIRFIELD),
            'stand' => $row->get(self::INDEXED_STAND),
            'callsign' => $this->emptyStringToNull($row->get(self::INDEXED_CALLSIGN)),
            'cid' => $this->emptyStringToNull($row->get(self::INDEXED_CID)),
            'origin' => $this->emptyStringToNull($row->get(self::INDEXED_ORIGIN)),
            'destination' => $this->emptyStringToNull($row->get(self::INDEXED_DESTINATION)),
            'start' => $row->get(self::INDEXED_START),
            'end' => $row->get(self::INDEXED_END),
        ];
    }

    private function emptyStringToNull(mixed $value): mixed
    {
        return $value === '' ? null : $value;
    }

    private function rowValid(array $reservationData): bool
    {
        try {
            return isset($reservationData['airfield']) &&
                array_key_exists($reservationData['airfield'], $this->stands) &&
                isset($reservationData['stand']) &&
                array_key_exists($reservationData['stand'], $this->stands[$reservationData['airfield']]) &&
                isset($reservationData['start']) &&
                ($startTime = Carbon::parse($reservationData['start'])) &&
                isset($reservationData['end']) &&
                ($endTime = Carbon::parse($reservationData['end'])) &&
                $endTime > $startTime;
        } catch (Exception $exception) {
            return false;
        }
    }
}
