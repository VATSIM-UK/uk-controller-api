<?php

namespace App\Services;

use App\Exceptions\Network\NetworkMetadataInvalidException;
use App\Helpers\Http\MakesHttpRequests;
use Exception;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class NetworkMetadataService
{
    use MakesHttpRequests;

    private const DATA_URL_CACHE_DURATION = 600;
    private const DATA_URL_CACHE_KEY = 'NETWORK_DATA_URL';
    private const NETWORK_METADATA_URL = "https://status.vatsim.net/status.json";

    public function getNetworkDataUrl(): string
    {
        return Cache::remember(
            self::DATA_URL_CACHE_KEY,
            self::DATA_URL_CACHE_DURATION,
            function () {
                try {
                    $networkResponse = $this->httpRequest()->get(self::NETWORK_METADATA_URL);
                } catch (Exception $exception) {
                    throw new NetworkMetadataInvalidException(
                        'Network metadata download failed: ' . $exception->getMessage()
                    );
                }

                if (!$networkResponse->successful()) {
                    throw new NetworkMetadataInvalidException(
                        'Network metadata response unsuccessful: ' . $networkResponse->status()
                    );
                }

                $metadataValidator = $this->networkDataResponseValidator($networkResponse);
                if ($metadataValidator->fails()) {
                    throw new NetworkMetadataInvalidException(
                        'Network metadata invalid, messages: ' . json_encode(
                            $metadataValidator->errors()->toArray()
                        )
                    );
                }

                return $metadataValidator->validated()['data']['v3'][0];
            }
        );
    }

    private function networkDataResponseValidator(Response $response): ValidatorContract
    {
        return Validator::make(
            $response->json(),
            [
                'data' => 'required|array',
                'data.v3' => 'required|array',
                'data.v3.*' => 'url'
            ]
        );
    }
}
