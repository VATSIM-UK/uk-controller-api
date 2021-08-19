<?php

namespace App\Services\Acars;

use App\Exceptions\Acars\AcarsRequestException;
use App\Helpers\Acars\TelexMessageInterface;
use App\Models\Acars\AcarsMessage;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HoppieAcarsProvider implements AcarsProviderInterface
{
    private const VATUK_STATION_IDENTIFIER = 'VATSIMUK';

    public function SendTelex(TelexMessageInterface $message): void
    {
        $this->makeRequest('telex', $message->getTarget(), $message->getMessage());
    }

    public function GetOnlineCallsigns(): Collection
    {
        $responseBody = $this->getResponseBody(
            $this->makeRequest('ping', self::VATUK_STATION_IDENTIFIER, 'ALL-CALLSIGNS')
        );
        return collect($responseBody === '' ? [] : explode(' ', $responseBody));
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

                AcarsMessage::create(
                    [
                        'message' => http_build_query($message),
                        'successful' => $success,
                    ]
                );

                if (!$success) {
                    throw new AcarsRequestException('Acars request failed, response: ' . $response->body());
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

    private function responseSuccessful(Response $response): bool
    {
        return $response->successful() && Str::substr($response, 0, 2) === 'ok';
    }
}
