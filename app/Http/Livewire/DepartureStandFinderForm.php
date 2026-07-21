<?php

namespace App\Http\Livewire;

use App\Filament\Helpers\SelectOptions;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Services\AirlineService;
use App\Services\AircraftService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
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

    private function baseQuery(Airfield $airfield, Aircraft $aircraft): Builder
    {
        return Stand::where('airfield_id', $airfield->id)
            ->available()
            ->sizeAppropriate($aircraft)
            ->select('stands.*');
    }

    private function ordered(Builder $query, array $prependOrders = []): Builder
    {
        foreach (
            array_merge($prependOrders, [
                'stands.aerodrome_reference_code',
                'stands.assignment_priority',
                'stands.identifier',
            ]) as $order
        ) {
            $query->orderBy($order);
        }

        return $query;
    }

    private function findStand(Airfield $airfield, Aircraft $aircraft, ?int $airlineId): array
    {
        $stand = $airlineId ? $this->tryAirlineAllocators($airfield, $aircraft, $airlineId) : null;

        $stand ??= $this->tryOriginSlug($airfield, $aircraft);
        $stand ??= $this->tryFallback($airfield, $aircraft);

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

    private function tryAirlineAllocators(Airfield $airfield, Aircraft $aircraft, int $airlineId): ?Stand
    {
        $slug = app()->make(AirlineService::class)->getCallsignSlugForAircraft($this->callsign);

        $slugs = [];
        for ($i = 0; $i < Str::length($slug); $i++) {
            $slugs[] = Str::substr($slug, 0, $i + 1);
        }

        $steps = [
            fn (Builder $q) => $q->where('airline_stand.full_callsign', $slug),
            fn (Builder $q) => $q->whereIn('airline_stand.callsign_slug', $slugs)
                ->orderByRaw('LENGTH(airline_stand.callsign_slug) DESC'),
            fn (Builder $q) => $q->where('airline_stand.aircraft_id', $aircraft->id),
        ];

        $steps[] = fn (Builder $q) => $q->whereNull('airline_stand.destination')
            ->whereNull('airline_stand.callsign_slug')
            ->whereNull('airline_stand.full_callsign')
            ->whereNull('airline_stand.aircraft_id');

        foreach ($steps as $step) {
            $stand = $this->ordered(
                $step($this->baseQuery($airfield, $aircraft)->airline($airlineId))
                    ->orderBy('airline_stand.priority'),
                ['airline_stand.priority']
            )->first();

            if ($stand) {
                return $stand;
            }
        }

        return null;
    }

    private function tryOriginSlug(Airfield $airfield, Aircraft $aircraft): ?Stand
    {
        $originSlugs = [
            Str::substr($this->departureAirfield, 0, 1),
            Str::substr($this->departureAirfield, 0, 2),
            Str::substr($this->departureAirfield, 0, 3),
            $this->departureAirfield,
        ];

        return $this->ordered(
            $this->baseQuery($airfield, $aircraft)
                ->notCargo()
                ->whereIn('stands.origin_slug', $originSlugs)
                ->orderByRaw('LENGTH(stands.origin_slug) DESC')
        )->first();
    }

    private function tryFallback(Airfield $airfield, Aircraft $aircraft): ?Stand
    {
        return $this->ordered(
            $this->baseQuery($airfield, $aircraft)->notCargo()
        )->first();
    }

    public function render()
    {
        return view('livewire.departure-stand-finder-form');
    }
}
