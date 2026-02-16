<?php

namespace App\Services\Metar;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetarRetrievalService
{
    public function retrieveMetars(Collection $airfields): Collection
    {
        if ($this->shouldSkipDueToBackoff()) {
            return collect();
        }

        $metarResponse = Http::get($this->getMetarUrl($airfields));
        if (!$metarResponse->ok()) {
            $this->recordServerError();

            Log::error(
                sprintf(
                    'Metar download failed, endpoint returned %d: %s',
                    $metarResponse->status(),
                    $metarResponse->body()
                )
            );

            return collect();
        }

        Cache::put('metar_error_count', Cache::get('metar_error_count', 0) - 1, now()->addHours(2));
        Cache::forget('metar_backoff_until');

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
        return sprintf(
            '%s%s%s',
            config('metar.vatsim_url'),
            "?id=",
            $this->getMetarQueryString($airfields)
        );
    }

    private function getMetarQueryString(Collection $airfields): string
    {
        return $airfields->concat([Carbon::now()->timestamp])->implode(',');
    }

    private function shouldSkipDueToBackoff(): bool
    {
        $backoffUntil = Cache::get('metar_backoff_until');
        
        if ($backoffUntil && Carbon::parse($backoffUntil)->isFuture()) {
            return true;
        }
        
        return false;
    }

    private function recordServerError(): void
    {
        $failureCount = Cache::get('metar_error_count', 0) + 1;
        
        $backoffMinutes = min(60, pow(2, $failureCount - 1));
        
        Cache::put('metar_error_count', $failureCount, now()->addHours(2));
        Cache::put('metar_backoff_until', now()->addMinutes($backoffMinutes), now()->addHours(2));
    }
}
