<?php

namespace App\Acars\Provider;

use App\Acars\Exception\AcarsRequestException;
use App\Acars\Message\Telex\TelexMessageInterface;
use App\Jobs\Acars\SendTelex;
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
    private const VATUK_STATION_IDENTIFIER = 'VATSIMUK';

    /**
     * Dispatches a job to send a telex message. We dispatch a job because we need to avoid
     * Hoppie rate limiting.
     *
     * @param TelexMessageInterface $message
     * @return void
     */
    public function sendTelex(TelexMessageInterface $message): void
    {
        if (!$this->getOnlineCallsigns()->contains($message->getTarget())) {
            return;
        }

        SendTelex::dispatch($message);
    }

    /**
     * Used by the SendTelex job to send a telex message. This method is intended for internal use and
     * is not exposed on the interface.
     */
    public function sendTelexMessage(TelexMessageInterface $message): void
    {
        $this->makeRequest('telex', $message->getTarget(), $message->getBody());
    }

    private function getOnlineCallsigns(): Collection
    {
        return Cache::get(
            self::ONLINE_CALLSIGNS_CACHE_KEY,
            collect()
        );
    }

    public function setOnlineCallsigns(): void
    {
        Cache::forever(
            self::ONLINE_CALLSIGNS_CACHE_KEY,
            fn() => $this->fetchOnlineCallsigns()
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
            function (Response $response) use ($message)
            {
                $success = $this->responseSuccessful($response);
                $messageWithoutLogon = array_filter(
                    $message,
                    fn(string $key) => $key !== 'logon',
                    ARRAY_FILTER_USE_KEY
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

    private function fetchOnlineCallsigns(): Collection
    {
        $responseBody = $this->getResponseBody(
            $this->makeRequest('ping', self::VATUK_STATION_IDENTIFIER, 'ALL-CALLSIGNS')
        );
        return collect($responseBody === '' ? [] : explode(' ', $responseBody));
    }
}
