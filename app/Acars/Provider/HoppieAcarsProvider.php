<?php

namespace App\Acars\Provider;

use App\Acars\Exception\AcarsRequestException;
use App\Acars\Message\Telex\TelexMessageInterface;
use App\Models\Acars\AcarsMessage;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HoppieAcarsProvider implements AcarsProviderInterface
{
    private const ONLINE_CALLSIGNS_CACHE_KEY = 'HOPPIE_ACARS_ONLINE_CALLSIGNS';
    private const ONLINE_CALLSIGNS_CACHE_DURATION_SECONDS = 120;
    private const VATUK_STATION_IDENTIFIER = 'VATSIMUK';

    public function sendTelex(TelexMessageInterface $message): void
    {
        if ($this->getOnlineCallsigns()->doesntContain($message->getTarget())) {
            return;
        }

        $this->makeRequest('telex', $message->getTarget(), $message->getBody());
    }

    private function getOnlineCallsigns(): Collection
    {
        return Cache::remember(
            self::ONLINE_CALLSIGNS_CACHE_KEY,
            self::ONLINE_CALLSIGNS_CACHE_DURATION_SECONDS,
            function () {
                $responseBody = $this->getResponseBody(
                    $this->makeRequest('ping', self::VATUK_STATION_IDENTIFIER, 'ALL-CALLSIGNS')
                );
                return collect($responseBody === '' ? [] : explode(' ', $responseBody));
            }
        );
    }

    private function makeRequest(string $messageType, string $target, string $data): Response
    {
        $message = $this->buildRequestBody($messageType, $target, $data);
        return tap(
            Http::asForm()->timeout(3)->post(
                config('acars.hoppie.url'),
                $message
            ),
            function (Response $response) use ($message) {
                $success = $this->responseSuccessful($response);
                $messageWithoutLogon = array_filter(
                    $message,
                    fn(string $key) => $key !== 'logon', ARRAY_FILTER_USE_KEY
                );

                AcarsMessage::create(
                    [
                        'message' => http_build_query($messageWithoutLogon),
                        'successful' => $success,
                    ]
                );

                if (!$success) {
                    $errorMessage = sprintf('Acars request failed, response: %s', $response->body());
                    Log::error($errorMessage);
                    throw new AcarsRequestException($errorMessage);
                }
            }
        );
    }

    private function buildRequestBody(string $messageType, string $target, string $data): array
    {
        return [
            'logon' => config('acars.hoppie.login_code'),
            'type' => $messageType,
            'to' => $target,
            'from' => self::VATUK_STATION_IDENTIFIER,
            'packet' => $data,
        ];
    }

    private function getResponseBody(Response $response): string
    {
        return Str::substr($response->body(), 4, -1);
    }

    private function responseSuccessful(Response $response): bool
    {
        return $response->successful() && Str::substr($response, 0, 2) === 'ok';
    }
}
