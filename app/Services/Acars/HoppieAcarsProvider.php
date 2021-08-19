<?php

namespace App\Services\Acars;

use App\Exceptions\Acars\AcarsRequestException;
use App\Helpers\Acars\TelexMessageInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HoppieAcarsProvider implements AcarsProviderInterface
{
    private const VATUK_STATION_IDENTIFIER = 'VATSIMUK';

    public function SendTelex(TelexMessageInterface $message): void
    {
        $this->makeRequest('telex', $message->getTarget(), $message->getMessage());
    }

    public function GetOnlineCallsigns(): array
    {
        $responseBody = $this->getResponseBody(
            $this->makeRequest('ping', self::VATUK_STATION_IDENTIFIER, 'ALL-CALLSIGNS')
        );
        return $responseBody === '' ? [] : explode(' ', $responseBody);
    }

    private function makeRequest(string $messageType, string $target, string $data): Response
    {
        return tap(
            Http::asForm()->post(
                config('acars.hoppie.url'),
                $this->buildRequestBody($messageType, $target, $data)
            ),
            function (Response $response) {
                if (!$response->successful()) {
                    throw new AcarsRequestException('Acars request failed, message: ' . $response->body());
                }

                if (Str::substr($response, 0, 2) !== 'ok') {
                    throw new AcarsRequestException('Acars response not ok, message: ' . $response->body());
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
            'from' => 'VATSIMUK',
            'packet' => $data
        ];
    }

    private function getResponseBody(Response $response): string
    {
        return Str::substr($response->body(), 4, -1);
    }
}
