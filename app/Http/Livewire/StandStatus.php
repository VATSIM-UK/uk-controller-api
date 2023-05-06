<?php

namespace App\Http\Livewire;

use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\Stand\StandStatusService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StandStatus extends Component
{
    public ?Stand $stand;

    public array $standStatus = ['available' => null, 'statusString' => ''];

    protected $listeners = [
        'updateStandStatus',
    ];

    public function mount(): void
    {
        $this->updateStandStatus($this->stand);
    }

    public function updateStandStatus(Stand $stand): void
    {
        $this->stand = $stand;
        $this->standStatus = $this->getStandStatus(
            $this->stand,
            $this->getUserAircraft()
        );
    }

    private function getStandStatus(Stand $stand, NetworkAircraft $userAircraft): array
    {
        $standStatus = StandStatusService::getStandStatus(Stand::findOrFail($stand->id));

        return match ($standStatus['status']) {
            'occupied' => $this->checkStandUnavailableReasonAndReturn(
                $standStatus,
                $userAircraft,
                'This stand is currently occupied by your aircraft.',
                'This stand is currently occupied by another aircraft.'
            ),
            'assigned' => $this->checkStandUnavailableReasonAndReturn(
                $standStatus,
                $userAircraft,
                'This stand is currently assigned to you.',
                'This stand is currently assigned to another aircraft.'
            ),
            'reserved' => $this->checkStandUnavailableReasonAndReturn(
                $standStatus,
                $userAircraft,
                'This stand is currently reserved for you.',
                'This stand is currently reserved for another aircraft.'
            ),
            'unavailable' => $this->getStandUnavailableReturn(
                'This stand is currently unavailable, it may be that another neighbouring stand is occupied, preventing this stand from being assigned.'
            ),
            'reserved_soon' => $this->checkStandUnavailableReasonAndReturn(
                $standStatus,
                $userAircraft,
                'This stand will soon be reserved for you.',
                sprintf(
                    'This stand is available, but is reserved for another aircraft at %s.',
                    $standStatus['reserved_at']->format('H:m\Z')
                )
            ),
            'requested' => $this->getStandRequestedReturn($standStatus, $userAircraft),
            default => $this->getStandStatusReturn(true, 'This stand is currently available.'),
        };
    }

    private function getStandRequestedReturn(array $standStatus, NetworkAircraft $userAircraft): array
    {
        if (count($standStatus['requested_by']) === 1 && $standStatus['requested_by'][0] === $userAircraft->callsign) {
            return $this->getStandIsUsersReturn('This stand is currently available.');
        }

        return $this->getStandUnavailableReturn(
            'This stand has been requested by multiple aircraft. It will be assigned on a first-come first-served basis.'
        );
    }

    private function getStandUnavailableReturn(string $messageString): array
    {
        return $this->getStandStatusReturn(
            false,
            sprintf(
                '%s %s',
                $messageString,
                'You may still request this stand, but it will not be assigned to you if it is still unavailable when your stand assignment is made.'
            )
        );
    }

    private function checkStandUnavailableReasonAndReturn(
        array $standStatus,
        NetworkAircraft $aircraft,
        string $messageIfUser,
        string $messageIfUnavailable
    ): array {
        return $this->standStatusRelatesToUser($standStatus, $aircraft)
            ? $this->getStandIsUsersReturn($messageIfUser)
            : $this->getStandUnavailableReturn($messageIfUnavailable);
    }

    private function standStatusRelatesToUser(array $standStatus, NetworkAircraft $userAircraft): bool
    {
        return $standStatus['callsign'] === $userAircraft->callsign;
    }

    private function getStandStatusReturn(bool $available, string $statusString): array
    {
        return ['available' => $available, 'statusString' => $statusString];
    }

    private function getStandIsUsersReturn(string $message): array
    {
        return ['available' => true, 'statusString' => $message];
    }

    private function getUserAircraft(): NetworkAircraft
    {
        return NetworkAircraft::where('cid', Auth::id())
            ->firstOrFail();
    }
}
