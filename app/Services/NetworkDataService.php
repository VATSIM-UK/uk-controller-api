<?php

namespace App\Services;

use App\Rules\Airfield\AirfieldIcao;
use App\Rules\Coordinates\Latitude;
use App\Rules\Coordinates\Longitude;
use App\Rules\Squawk\SqauwkCode;
use App\Rules\VatsimCallsign;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NetworkDataService
{
    private NetworkDataDownloadService $downloadService;

    public function __construct(NetworkDataDownloadService $downloadService)
    {
        $this->downloadService = $downloadService;
    }

    public function getNetworkAircraftData(): Collection
    {
        $fieldValidator = $this->getFieldValidator('pilots');
        if ($fieldValidator->fails()) {
            Log::warning('Invalid network aircraft data, pilots field missing');
            return collect();
        }

        $validatedData = collect($fieldValidator->validated()['pilots']);
        return $validatedData->reject(function (array $data) {
            return $this->getAircraftValidator($data)->fails();
        });
    }

    public function getNetworkControllerData(): Collection
    {
        $fieldValidator = $this->getFieldValidator('controllers');
        if ($fieldValidator->fails()) {
            Log::warning('Invalid network controller data, controllers field missing');
            return collect();
        }

        $validatedData = collect($fieldValidator->validated()['controllers']);
        return $validatedData->reject(function (array $data) {
            return $this->getControllerValidator($data)->fails();
        });
    }

    private function getFieldValidator(string $field): ValidatorContract
    {
        return Validator::make(
            $this->downloadService->getNetworkData()->toArray(),
            [
                $field => 'array|required'
            ]
        );
    }

    private function getAircraftValidator(array $data): ValidatorContract
    {
        return Validator::make(
            $data,
            [
                'callsign' => [
                    'required',
                    new VatsimCallsign(),
                ],
                'latitude' => [
                    'required',
                    new Latitude(),
                ],
                'longitude' => [
                    'required',
                    new Longitude(),
                ],
                'altitude' => 'required|integer',
                'groundspeed' => 'required|integer',
                'transponder' => [
                    'required',
                    new SqauwkCode(),
                ],
                'flight_plan.aircraft' => 'nullable|string',
                'flight_plan.departure' => [
                    'nullable',
                    new AirfieldIcao(),
                ],
                'flight_plan.arrival' => [
                    'nullable',
                    new AirfieldIcao(),
                ],
                'flight_plan.altitude' => 'nullable|string',
                'flight_plan.flight_rules' => 'nullable|string',
            ]
        );
    }

    private function getControllerValidator(array $data): ValidatorContract
    {
        return Validator::make(
            $data,
            [
                'callsign' => [
                    'required',
                    new VatsimCallsign(),
                ],
                'frequency' => 'required|numeric|min:100|max:200',
                'cid' => 'integer|required',
            ]
        );
    }
}
