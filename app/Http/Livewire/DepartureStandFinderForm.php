<?php

namespace App\Http\Livewire;

use App\Allocator\Stand\StandAllocationType;
use App\Filament\Helpers\SelectOptions;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use App\Services\AircraftService;
use App\Services\Stand\ArrivalAllocationService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class DepartureStandFinderForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?string $callsign = null;
    public ?string $departureAirfield = null;
    public ?int $aircraftType = null;
    public ?array $prefiledFlightplan = null;

    public function mount(): void
    {
        $this->tryLoadPrefiledFlightplan();
    }

    private function tryLoadPrefiledFlightplan(): void
    {
        $cid = Auth::id();
        $rawData = Cache::get('vatsim_raw_data');

        if (!$rawData) {
            return;
        }

        foreach ($rawData['pilots'] ?? [] as $pilot) {
            if (($pilot['cid'] ?? null) == $cid && !empty($pilot['flight_plan'])) {
                $fp = $pilot['flight_plan'];
                $this->prefiledFlightplan = [
                    'callsign' => $pilot['callsign'] ?? null,
                    'departure' => $fp['departure'] ?? null,
                    'arrival' => $fp['arrival'] ?? null,
                    'aircraft_short' => $fp['aircraft_short'] ?? null,
                ];
                $this->callsign = $this->prefiledFlightplan['callsign'];
                $this->departureAirfield = $this->prefiledFlightplan['departure'];
                $aircraftId = app()->make(AircraftService::class)->getAircraftIdFromCode($this->prefiledFlightplan['aircraft_short']);
                if ($aircraftId) {
                    $this->aircraftType = $aircraftId;
                }
                return;
            }
        }
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('callsign')
                ->label('Callsign')
                ->placeholder('BAW123')
                ->required()
                ->afterStateUpdated(fn () => $this->resetValidation('callsign')),
            Select::make('departureAirfield')
                ->label('Departure Airfield')
                ->options(Airfield::all()->mapWithKeys(fn (Airfield $airfield) => [$airfield->code => $airfield->code]))
                ->required()
                ->searchable(),
            Select::make('aircraftType')
                ->label('Aircraft Type')
                ->options(SelectOptions::aircraftTypes())
                ->required()
                ->searchable(),
        ];
    }

    public function submit(): void
    {
        $this->callsign = strtoupper($this->callsign);
        $this->departureAirfield = strtoupper($this->departureAirfield);

        $this->form->validate();

        $airfield = Airfield::fromCode($this->departureAirfield);
        $aircraft = Aircraft::findOrFail($this->aircraftType);
        $airlineId = app()->make(AirlineService::class)->airlineIdForCallsign($this->callsign);

        $this->dispatch('departureStandFinderFormSubmitted', $this->findStand($airfield, $aircraft, $airlineId));
    }

    private function findStand(Airfield $airfield, Aircraft $aircraft, ?int $airlineId): array
    {
        $networkAircraft = new NetworkAircraft([
            'callsign' => $this->callsign,
            'cid' => Auth::id(),
            'planned_depairport' => $airfield->code,
            'planned_destairport' => $this->prefiledFlightplan['arrival'] ?? null,
            'planned_aircraft_short' => $aircraft->code,
            'aircraft_id' => $aircraft->id,
            'airline_id' => $airlineId,
        ]);

        $stand = $this->findDepartureStand($networkAircraft);

        if (!$stand) {
            return [
                'error' => sprintf(
                    'No available stand found at %s that fits the %s.',
                    $airfield->code,
                    $aircraft->code
                ),
            ];
        }

        return [
            'stand' => [
                'identifier' => $stand->identifier,
                'airfield' => $stand->airfield->code,
                'terminal' => $stand->terminal?->description,
                'type' => $stand->type?->key,
                'aerodrome_reference_code' => $stand->aerodrome_reference_code,
                'max_aircraft_wingspan' => $stand->max_aircraft_wingspan,
                'max_aircraft_length' => $stand->max_aircraft_length,
            ],
        ];
    }

    private function findDepartureStand(NetworkAircraft $aircraft): ?Stand
    {
        foreach (app()->make(ArrivalAllocationService::class)->getAllocators() as $allocator) {
            if ($standId = $allocator->allocate($aircraft, StandAllocationType::Departure)) {
                return Stand::find($standId);
            }
        }

        return null;
    }

    public function render()
    {
        return view('livewire.departure-stand-finder-form');
    }
}
