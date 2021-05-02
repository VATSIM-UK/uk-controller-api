<?php

namespace App\Services;

use App\Events\MetarsUpdatedEvent;
use App\Models\Airfield\Airfield;
use App\Models\Metars\Metar;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service for parsing data in relation to METARs.
 *
 * Class MetarService
 * @package App\Services
 */
class MetarService
{
    private function getMetarQueryString(Collection $airfields): string
    {
        return $airfields->implode('code', ',');
    }

    public function updateAllMetars(): void
    {
        $metarAirfields = Airfield::all();
        $metarResponse = Http::get(config('metar.vatsim_url'), ['id' => $this->getMetarQueryString($metarAirfields)]);
        if (!$metarResponse->ok()) {
            Log::error(
                sprintf(
                    'Metar download failed, endpoint returned %d: %s',
                    $metarResponse->status(),
                    $metarResponse->body()
                )
            );
            return;
        }

        $metarsToUpdate = [];
        foreach (explode("\n", $metarResponse->body()) as $metar) {
            $metarsToUpdate[] = $this->processMetar($metar, $metarAirfields);
        }

        Metar::upsert(
            $metarsToUpdate,
            ['airfield_id']
        );

        event(new MetarsUpdatedEvent(Metar::with('airfield')->get()));
    }

    /**
     * Process a given METAR and make it ready for inserting.
     */
    private function processMetar(string $metar, Collection $metarAirfields): array
    {
        return [
            'airfield_id' => $metarAirfields->where('code', $this->getMetarAirfield($metar))->first()->id,
            'raw' => trim($metar),
            'parsed' => json_encode([
                'qnh' => $this->getQnhFromMetar($metar)
            ]),
        ];
    }

    /**
     * Try to get the QNH out of the METAR, if it's not there, parse it from the altimeter.
     */
    private function getQnhFromMetar(string $metar): ?int
    {
        return $this->parseQnhFromMetar($metar) ?? $this->parseAltimeterAsQnhFromMetar($metar);
    }

    /**
     * Parse the QNH string from the METAR
     */
    private function parseQnhFromMetar(string $metar): ?int
    {
        $matches = [];
        preg_match('/Q(\d{4})/', $metar, $matches);
        return empty($matches) ? null : $this->convertQnhToInteger($matches[1]);
    }

    /**
     * Parse the altimeter out of the METAR as its QNH.
     */
    private function parseAltimeterAsQnhFromMetar(string $metar): ?int
    {
        $matches = [];
        preg_match('/A(\d{4})/', $metar, $matches);
        return empty($matches) ? null : $this->convertAltimeterToQnh($matches[1]);
    }

    private function convertAltimeterToQnh(string $altimeter): int
    {
        return ((float) sprintf('%s.%s', substr($altimeter, 0, 2), substr($altimeter, 2))) * 33.8639;
    }

    private function convertQnhToInteger(string $qnh): int
    {
        return (int) ($qnh[0] === '0') ? substr($qnh, 1) : $qnh;
    }

    private function getMetarAirfield(string $metar): string
    {
        return substr($metar, 0, 4);
    }
}
