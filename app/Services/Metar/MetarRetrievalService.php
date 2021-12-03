<?php

namespace App\Services\Metar;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MetarRetrievalService
{
    private MetarTokeniser $tokeniser;

    public function __construct(MetarTokeniser $tokeniser)
    {
        $this->tokeniser = $tokeniser;
    }

    public function retrieveMetars(Collection $airfields): Collection
    {
        $metarResponse = Http::get(config('metar.vatsim_url'), ['id' => $this->getMetarQueryString($airfields)]);
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
            ->mapWithKeys(function (string $metar) {
                $tokens = $this->tokeniser->tokenise($metar);
                return [
                    $tokens->first() => $tokens
                ];
            });
    }

    private function getMetarQueryString(Collection $airfields): string
    {
        return $airfields->implode(',');
    }
}
