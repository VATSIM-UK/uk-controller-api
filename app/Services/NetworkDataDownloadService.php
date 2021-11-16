<?php

namespace App\Services;

use App\Exceptions\Network\NetworkMetadataInvalidException;
use App\Helpers\Http\MakesHttpRequests;
use Exception;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NetworkDataDownloadService
{
    use MakesHttpRequests;

    private NetworkMetadataService $metadataService;
    private Collection $networkData;

    public function __construct(NetworkMetadataService $metadataService)
    {
        $this->metadataService = $metadataService;
        $this->networkData = new Collection();
    }

    public function getNetworkData(): Collection
    {
        if ($this->networkData->isEmpty()) {
            $this->networkData = $this->downloadNetworkData();
        }

        return $this->networkData;
    }

    private function downloadNetworkData(): Collection
    {
        try {
            $networkResponse = $this->httpRequest()->get($this->metadataService->getNetworkDataUrl());
        } catch (Exception $exception) {
            Log::warning('Failed to download network data, exception was ' . $exception->getMessage());
            return new Collection();
        }

        if (!$networkResponse->successful()) {
            Log::warning('Failed to download network data, response was ' . $networkResponse->status());
            return new Collection();
        }

        return collect($networkResponse->json());
    }
}
