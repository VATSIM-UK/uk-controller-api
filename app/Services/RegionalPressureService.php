<?php
namespace App\Services;

use App\Exceptions\MetarException;
use App\Helpers\AltimeterSettingRegions\AltimeterSettingRegion;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;

/**
 * Service for generating the regional pressure settings based on the airfields
 * that comprise each Altimeter Setting Region. Also responsible for caching the values
 * to be used by the controllers when clients request this data.
 *
 * Class RegionalPressureService
 * @package App\Services
 */
class RegionalPressureService
{
    // HTTP Client for making requests
    private $http;

    // Altimeter setting regions
    private $regions;

    // The URI to get the METAR data from
    private $metarUri;

    // The error last error
    private $lastError;

    // Parses parts of METARs for us.
    private $metarParser;

    // The error constant for a request failure
    const ERROR_REQUEST_FAILED = "requestFailed";

    // The error constant for invalid XML
    const ERROR_INVALID_XML = "invalidXml";

    // The cache key for our regional pressures
    const RPS_CACHE_KEY = 'regional_pressures';

    // Error strings
    const ERROR_DESCRIPTIONS = [
        self::ERROR_REQUEST_FAILED => 'Data request failed.',
        self::ERROR_INVALID_XML => 'Failed to retrieve METAR data: host returned invalid XML.',
    ];

    /**
     * RegionalPressureService constructor.
     *
     * @param Client       $http        HTTP Client
     * @param string       $metarUri    The URI to retrieve METARs from
     * @param MetarService $metarParser A service to parse METARs for us
     * @param array        $regions     The Altimeter setting regions
     */
    public function __construct(Client $http, string $metarUri, MetarService $metarParser, array $regions)
    {
        $this->http = $http;
        $this->metarUri = $metarUri;
        $this->regions = $regions;
        $this->metarParser = $metarParser;
    }

    /**
     * Generates the regional pressure settings and caches them.
     *
     * @return bool true on success, false on failure.
     */
    public function generateRegionalPressures() : bool
    {
        // Request from the server and check it's valid.
        $xml = $this->getResponse();
        if (!$xml) {
            return false;
        }

        // Get the regional pressures and cache
        Cache::forever(self::RPS_CACHE_KEY, $this->calculateRegionalPressures($this->parsePressuresFromXML($xml)));
        return true;
    }

    /**
     * Returns the regional pressures from the cache, or an empty array if none found.
     *
     * @return array
     */
    public function getRegionalPressuresFromCache() : array
    {
        $value = Cache::get(self::RPS_CACHE_KEY, []);
        return (is_array($value)) ? $value : [];
    }

    /**
     * Parses all the pressures from the XML.
     *
     * @param  SimpleXMLElement $xml XML to be parsed
     * @return array Array of airfield => pressure
     */
    private function parsePressuresFromXML(SimpleXMLElement $xml) : array
    {
        $pressures = [];
        // Loop each airfield and find its QNH;
        foreach ($xml->data->children() as $airfield) {
            try {
                $pressures[(string) $airfield->station_id] =
                    $this->metarParser->getQnhFromMetar((string) $airfield->raw_text);
            } catch (MetarException $e) {
                $pressures[(string) $airfield->station_id] = AltimeterSettingRegion::DEFAULT_MIN_QNH;
            }
        }

        return $pressures;
    }

    /**
     * Calculates the regional pressures, based on the lowest QNH available.
     *
     * @param  array $pressures
     * @return array
     */
    private function calculateRegionalPressures(array $pressures) : array
    {
        // Loop the regions and calculate the RPS.
        $regionalPressures = [];
        foreach ($this->regions as $region) {
            $regionalPressures[$region->getName()] = $region->calculateRegionalPressure($pressures);
        }

        return $regionalPressures;
    }

    /**
     * Checks the response we get from the METAR server.
     *
     * @param  ResponseInterface $response PSR-7 response from the server
     * @return bool|SimpleXMLElement False if something goes wrong, the XML otherwise.
     */
    private function getResponse()
    {
        // Make the request
        try {
            $response = $this->http->get($this->metarUri);
        } catch (ClientException $exception) {
            $this->lastError = self::ERROR_REQUEST_FAILED;
            return false;
        }

        // Check we have valid XML
        @$xml = simplexml_load_string($response->getBody()->getContents());
        if ($xml === false) {
            $this->lastError = self::ERROR_INVALID_XML;
        }

        return $xml;
    }

    /**
     * Returns the last error.
     *
     * @return string
     */
    public function getLastError() : string
    {
        return $this->lastError;
    }
}
