<?php

namespace App\Http\Livewire;

use App\Filament\Helpers\SelectOptions;
use App\Models\Airfield\Airfield;
use App\Rules\Airfield\AirfieldIcao;
use App\Services\AirlineService;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StandPredictorForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?string $callsign = null;

    public ?string $arrivalAirfield = null;
    public ?string $departureAirfield = null;
    public ?int $aircraftType = null;

    protected $messages = [
        'requestedStand' => 'You must select a valid stand.',
        'requestedTime' => 'Please enter a valid time.',
    ];

    public function getFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    TextInput::make('callsign')
                        ->placeholder('BAW123')
                        ->required()
                        ->label('Callsign'),
                    Select::make('aircraftType')
                        ->label('Aircraft Type')
                        ->options(SelectOptions::aircraftTypes())
                        ->required()
                        ->searchable(),
                    TextInput::make('departureAirfield')
                        ->label('Departure Airfield')
                        ->rule(new AirfieldIcao())
                        ->alpha()
                        ->required(),
                    Select::make('arrivalAirfield')
                        ->label('Arrival Airfield')
                        ->options(Airfield::all()->mapWithKeys(fn ($airfield) => [$airfield->code => $airfield->code]))
                        ->required()
                        ->searchable(),
                ])
        ];
    }

    public function submit(): void
    {
        // Convert the callsign to uppercase before validating it.
        $this->departureAirfield = strtoupper($this->departureAirfield);

        $this->form->validate();
        $this->emit('standPredictorFormSubmitted', [
            'callsign' => $this->callsign,
            'cid' => Auth::id(),
            'aircraft_id' => $this->aircraftType,
            'airline_id' => app()->make(AirlineService::class)->airlineIdForCallsign($this->callsign),
            'planned_depairport' => $this->departureAirfield,
            'planned_destairport' => $this->arrivalAirfield,
        ]);
    }
}
