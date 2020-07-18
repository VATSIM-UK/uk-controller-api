<?php
namespace App\Services;

use App\Exceptions\MetarException;
use App\Models\Airfield\Airfield;
use App\Models\AltimeterSettingRegions\AltimeterSettingRegion;
use App\Models\AltimeterSettingRegions\RegionalPressureSetting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;

/**
 * Service for generating the regional pressure settings based on the airfields
 * that comprise each Altimeter Setting Region.
 *
 * Class RegionalPressureService
 * @package App\Services
 */
class RegionalPressureService
{
    // HTTP Client for making requests
    private $http;

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

    // The lowest default QNH value
    const LOWEST_QNH_DEFAULT = 9999;

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
     */
    public function __construct(Client $http, string $metarUri, MetarService $metarParser)
    {
        $this->http = $http;
        $this->metarUri = $metarUri;
        $this->metarParser = $metarParser;
    }

    /**
     * Generate each of the regional pressures
     *
     * @return array|null
     */
    public function generateRegionalPressures() : ?array
    {
        // Request from the server and check it's valid.
        $xml = $this->getResponse();
        if (!$xml) {
            return null;
        }

        $airfieldQnh = $this->parsePressuresFromXML($xml);

        return AltimeterSettingRegion::with('airfields')->get()->mapWithKeys(
            function (AltimeterSettingRegion $asr) use ($airfieldQnh) {
                $regionalPressure = $this->calculateRegionalPressure($asr, $airfieldQnh);
                RegionalPressureSetting::updateOrCreate(
                    [
                        'altimeter_setting_region_id' => $asr->id,
                    ],
                    [
                        'value' => $regionalPressure,
                    ]
                );
                return [$asr->key => $this->calculateRegionalPressure($asr, $airfieldQnh)];
            }
        )->toArray();
    }

    public function calculateRegionalPressure(AltimeterSettingRegion $asr, array $airfieldQnhs) : ?int
    {
        $lowestQnh = $asr->airfields->reduce(function (int $carry, Airfield $item) use ($airfieldQnhs) {
            if (!isset($airfieldQnhs[$item->code])) {
                return $carry;
            }

            return $carry < $airfieldQnhs[$item->code]
                ? $carry
                : $airfieldQnhs[$item->code];
        }, self::LOWEST_QNH_DEFAULT);

        return $lowestQnh !== self::LOWEST_QNH_DEFAULT ? $lowestQnh - 1 : self::LOWEST_QNH_DEFAULT;
    }

    public function getRegionalPressureArray() : array
    {
        return RegionalPressureSetting::with('altimeterSettingRegion')->get()->mapWithKeys(
            function (RegionalPressureSetting $rps) {
                return [
                    $rps->altimeterSettingRegion->key => $rps->value,
                ];
            }
        )->toArray();
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
        // Loop each airfield and find its QNH
        foreach ($xml->data->children() as $airfield) {
            try {
                $pressures[(string) $airfield->station_id] =
                    $this->metarParser->getQnhFromMetar((string) $airfield->raw_text);
            } catch (MetarException $e) {
                $pressures[(string) $airfield->station_id] = self::LOWEST_QNH_DEFAULT;
            }
        }

        return $pressures;
    }

    /**
     * Checks the response we get from the METAR server.
     *
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
