<?php

namespace App\Services;

use App\Exceptions\MetarException;
use App\Models\Airfield\Airfield;
use App\Models\Metars\Metar;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
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
    /**
     * @var Client
     */
    private $httpClient;

    /**
     * A cache of METARs that have been retrieved from VATSIM in this request
     *
     * @var array
     */
    private $metarCache = [];

    /**
     * MetarService constructor.
     * @param Client $httpClient
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Returns the QNH from a METAR.
     *
     * @param string $metar
     * @return mixed The QNH
     * @throws MetarException If the METAR doesn't have a QNH or has more than one
     */
    public function getQnhFromMetar(string $metar): int
    {
        $matches = [];
        preg_match('/Q\d{4}/', $metar, $matches);

        // Check for dodgy metars
        if (empty($matches)) {
            throw new MetarException('QNH not found in METAR: ' . $metar);
        }

        // Strip the Q and handle pressures < 1000hpa
        $value = substr($matches[0], 1);
        return (int)($value[0] === '0') ? substr($value, 1) : $value;
    }

    /**
     * Downloads a METAR from VATSIM and returns the QNH
     *
     * @param string $icao
     * @return int|null
     */
    public function getQnhFromVatsimMetar(string $icao): ?int
    {
        // Don't go and get it again if we already have iu
        if (isset($this->metarCache[$icao])) {
            return $this->getQnhFromMetar($this->metarCache[$icao]);
        }

        $metar = $this->httpClient->get(
            config('metar.vatsim_url'),
            [
                RequestOptions::ALLOW_REDIRECTS => true,
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY => [
                    'id' => $icao,
                ],
            ]
        );

        $metarString = (string)$metar->getBody();
        if (
            $metar->getStatusCode() !== 200 ||
            strpos($metarString, 'No METAR available for') === 0
        ) {
            Log::info('Failed to download METAR for ' . $icao);
            return null;
        }

        // Cache the METAR for later
        $this->metarCache[$icao] = $metarString;

        // Parse the QNH from the METAR
        $qnh = null;
        try {
            $qnh = $this->getQnhFromMetar($metarString);
        } catch (MetarException $exception) {
            Log::info(
                'Unable to get QNH from METAR',
                ['icao' => $icao, 'metar' => $metar->getBody()->getContents()]
            );
        }

        return $qnh;
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
            $metarsToUpdate[] = [
                'airfield_id' => $metarAirfields->where('code', $this->getMetarAirfield($metar))->first()->id,
                'metar_string' => $metar,
            ];
        }

        Metar::upsert(
            $metarsToUpdate,
            ['airfield_id'],
        );
    }

    private function getMetarQueryString(Collection $airfields): string
    {
        return $airfields->implode('code', ',');
    }

    private function getMetarAirfield(string $metar): string
    {
        return substr($metar, 0, 4);
    }
}
