<?php

namespace App\Services\Metar;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetarRetrievalService
{
    public function retrieveMetars(Collection $airfields): Collection
    {
        $metarResponse = Http::get($this->getMetarUrl($airfields));
        if (!$metarResponse->ok()) {
            Log::error(
                sprintf(
                    'Metar download failed, endpoint returned %d: %s',
                    $metarResponse->status(),
                    $metarResponse->body()
                )
            );

            return collect();
        }

        return collect(explode("\n", $metarResponse->body()))
            ->filter()
            ->mapWithKeys(function (string $metar) {
                $metarObject = new DownloadedMetar($metar);

                return [
                    $metarObject->tokenise()->first() => new DownloadedMetar($metar),
                ];
            })->filter();
    }

    private function getMetarUrl(Collection $airfields)
    {
        return sprintf('%s%s%s',
            config('metar.vatsim_url'),
            "?id=",
            $this->getMetarQueryString($airfields)
        );
    }

    private function getMetarQueryString(Collection $airfields): string
    {
        return $airfields->concat([Carbon::now()->timestamp])->implode(',');
    }
}
