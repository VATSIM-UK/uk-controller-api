<?php

namespace App\Helpers\Prenote;

use App\Http\Requests\Prenote\CreatePrenoteMessage;

final class CreatePrenoteMessageData
{
    private string $callsign;
    private string $departureAirfield;
    private ?string $departureSid;
    private ?string $destinationAirfield;
    private int $userId;
    private int $requestingControllerId;
    private int $targetControllerId;
    private int $expiresInSeconds;

    protected function __construct(array $validatedData, int $userId)
    {
        $this->callsign = $validatedData['callsign'];
        $this->departureAirfield = $validatedData['departure_airfield'];
        $this->departureSid = $validatedData['departure_sid'];
        $this->destinationAirfield = $validatedData['destination_airfield'];
        $this->userId = $userId;
        $this->requestingControllerId = $validatedData['requesting_controller_id'];
        $this->targetControllerId = $validatedData['target_controller_id'];
        $this->expiresInSeconds = $validatedData['expires_in_seconds'];
    }

    public static function fromRequest(
        array $validatedData,
        int $userId
    ): CreatePrenoteMessageData {
        return new static($validatedData, $userId);
    }

    public function getCallsign(): string
    {
        return $this->callsign;
    }

    public function getDepartureAirfield(): string
    {
        return $this->departureAirfield;
    }

    public function getDepartureSid(): ?string
    {
        return $this->departureSid;
    }

    public function getDestinationAirfield(): ?string
    {
        return $this->destinationAirfield;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getRequestingControllerId(): int
    {
        return $this->requestingControllerId;
    }

    public function getTargetControllerId(): int
    {
        return $this->targetControllerId;
    }

    public function getExpiresInSeconds(): int
    {
        return $this->expiresInSeconds;
    }
}
