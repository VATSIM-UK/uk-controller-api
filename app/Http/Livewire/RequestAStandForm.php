<?php

namespace App\Http\Livewire;

use App\Filament\Helpers\SelectOptions;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\Stand\StandRequest;
use App\Models\Stand\StandRequestHistory;
use App\Models\Vatsim\NetworkAircraft;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RequestAStandForm extends Component
{
    public ?NetworkAircraft $userAircraft;
    public array $stands = [];
    public ?int $requestedStand = null;
    public ?string $requestedTime = null;

    protected $messages = [
        'requestedStand' => 'You must select a valid stand.',
        'requestedTime' => 'You must select a valid time.',
    ];

    public function mount(): void
    {
        $this->userAircraft = $this->getUserAircraft();
        $userDestinationAirfield = $this->userAircraft ? Airfield::where(
            'code',
            $this->userAircraft->planned_destairport
        )
            ->first()
            : null;
        $this->stands = $userDestinationAirfield
            ? SelectOptions::standsForAirfield($userDestinationAirfield)->toArray()
            : [];
    }

    public function submit(): void
    {
        $validatedData = $this->validate(
            [
                'requestedStand' => [
                    'required',
                    'integer',
                    function (string $attribute, mixed $value, Closure $fail) {
                        if (!Stand::where('id', $value)->exists()){
                            $fail('Invalid stand.');
                        }
                    },
                ],
                'requestedTime' => 'required|string|date|after:now|before:+24 hours',
            ]
        );

        $userAircraft = $this->getUserAircraft();
        if (!$userAircraft) {
            return;
        }

        DB::transaction(function () use ($validatedData, $userAircraft) {
            $requestData = [
                'stand_id' => $validatedData['requestedStand'],
                'user_id' => Auth::id(),
                'callsign' => $userAircraft->callsign,
                'requested_time' => $validatedData['requestedTime'],
            ];

            $request = StandRequest::create($requestData);
            $historyItem = new StandRequestHistory($requestData);
            $historyItem->id = $request->id;
            $historyItem->save();
        });

        $this->emit('requestAStandFormSubmitted');
    }

    public function updatedRequestedStand(): void
    {
        if (($stand = $this->getStandProperty())) {
            $this->emitTo('stand-status', 'updateStandStatus', ['stand' => $stand]);
        }
    }

    public function getStandProperty(): ?Stand
    {
        return Stand::find($this->requestedStand);
    }

    private function getUserAircraft(): ?NetworkAircraft
    {
        return NetworkAircraft::where('cid', Auth::id())
            ->first();
    }
}
