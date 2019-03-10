<?php
namespace App\Services;

use App\Exceptions\MetarException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

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
     * @param  string $metar
     * @return mixed The QNH
     * @throws MetarException If the METAR doesn't have a QNH or has more than one
     */
    public function getQnhFromMetar(string $metar) : int
    {
        $matches = [];
        preg_match('/Q\d{4}/', $metar, $matches);

        // Check for dodgy metars
        if (count($matches) === 0) {
            throw new MetarException('QNH not found in METAR: ' . $metar);
        }

        // Strip the Q and handle pressures < 1000hpa
        $value = substr($matches[0], 1);
        return (int) ($value[0] === '0') ? substr($value, 1) : $value;
    }

    /**
     * Downloads a METAR from VATSIM and returns the QNH
     *
     * @param string $icao
     * @return int|null
     */
    public function getQnhFromVatsimMetar(string $icao) : ?int
    {
        $metar = $this->httpClient->get(
            env('VATSIM_METAR_URL'),
            [
                RequestOptions::ALLOW_REDIRECTS => true,
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY => [
                    'id' => $icao,
                ],
            ]
        );

        if ($metar->getStatusCode() !== 200) {
            Log::error('Failed to download METAR for ' . $icao);
            return null;
        }

        $metarString = (string) $metar->getBody();

        // No METAR available
        if (strpos($metarString, 'No METAR available for') === 0) {
            return null;
        }

        // Parse the QNH from the METAR
        $qnh = null;
        try {
            $qnh = $this->getQnhFromMetar($metarString);
        } catch (MetarException $exception) {
            Log::error(
                'Unable to get QNH from METAR',
                ['icao' => $icao, 'metar' => $metar->getBody()->getContents() ]
            );
        }

        return $qnh;
    }
}
