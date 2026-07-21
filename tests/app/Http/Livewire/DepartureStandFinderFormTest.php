<?php

namespace App\Http\Livewire;

use App\BaseFilamentTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

class DepartureStandFinderFormTest extends BaseFilamentTestCase
{
    use DatabaseTransactions;

    private Airfield $airfield;
    private Aircraft $aircraft;
    private string $icaoCode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icaoCode = 'EGXY';
        $this->airfield = Airfield::firstOrCreate(
            ['code' => $this->icaoCode],
            ['latitude' => 0, 'longitude' => 0, 'elevation' => 0, 'transition_altitude' => 3000, 'standard_high' => 1]
        );
        $this->aircraft = Aircraft::firstOrCreate(
            ['code' => 'B73X'],
            [
                'aerodrome_reference_code' => 'C',
                'wingspan' => 35.8,
                'length' => 39.5,
                'allocate_stands' => true,
            ]
        );
    }

    public function testItRenders()
    {
        Livewire::test(DepartureStandFinderForm::class)
            ->assertOk();
    }

    public function testItLoadsPrefiledFlightplanFromCache()
    {
        Cache::put('vatsim_raw_data', [
            'pilots' => [
                [
                    'cid' => 1203533,
                    'callsign' => 'BAW123',
                    'flight_plan' => [
                        'departure' => $this->icaoCode,
                        'arrival' => 'EDDF',
                        'aircraft_short' => 'B73X',
                    ],
                ],
            ],
        ], 120);

        Livewire::test(DepartureStandFinderForm::class)
            ->assertSet('callsign', 'BAW123')
            ->assertSet('departureAirfield', $this->icaoCode)
            ->assertSet('aircraftType', $this->aircraft->id)
            ->assertSet('prefiledFlightplan', [
                'callsign' => 'BAW123',
                'departure' => $this->icaoCode,
                'arrival' => 'EDDF',
                'aircraft_short' => 'B73X',
            ]);
    }

    public function testItShowsEmptyFormWhenNoPrefiledFlightplan()
    {
        Cache::put('vatsim_raw_data', ['pilots' => []], 120);

        Livewire::test(DepartureStandFinderForm::class)
            ->assertSet('callsign', null)
            ->assertSet('departureAirfield', null)
            ->assertSet('aircraftType', null)
            ->assertSet('prefiledFlightplan', null);
    }

    public function testItDoesNotLoadFlightplanForDifferentCid()
    {
        Cache::put('vatsim_raw_data', [
            'pilots' => [
                [
                    'cid' => 999999,
                    'callsign' => 'EZY456',
                    'flight_plan' => [
                        'departure' => 'EGKK',
                        'arrival' => 'EGPH',
                        'aircraft_short' => 'A320',
                    ],
                ],
            ],
        ], 120);

        Livewire::test(DepartureStandFinderForm::class)
            ->assertSet('callsign', null)
            ->assertSet('prefiledFlightplan', null);
    }

    public function testItSubmitsAndFindsStand()
    {
        $stand = Stand::factory()->create([
            'airfield_id' => $this->airfield->id,
            'identifier' => '123',
            'aerodrome_reference_code' => 'C',
            'assignment_priority' => 1,
        ]);

        Livewire::test(DepartureStandFinderForm::class)
            ->set('callsign', 'BAW999')
            ->set('departureAirfield', $this->icaoCode)
            ->set('aircraftType', $this->aircraft->id)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatched('departureStandFinderFormSubmitted', [
                'stand' => [
                    'identifier' => '123',
                    'airfield' => $this->icaoCode,
                    'terminal' => null,
                    'type' => null,
                    'aerodrome_reference_code' => 'C',
                    'max_aircraft_wingspan' => null,
                    'max_aircraft_length' => null,
                ],
            ]);
    }

    public function testItReturnsErrorWhenNoStandFound()
    {
        // No stands at this airfield
        Livewire::test(DepartureStandFinderForm::class)
            ->set('callsign', 'BAW999')
            ->set('departureAirfield', $this->icaoCode)
            ->set('aircraftType', $this->aircraft->id)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatched('departureStandFinderFormSubmitted', [
                'error' => 'No available stand found at EGXY that fits the B73X.',
            ]);
    }

    public function testItValidatesRequiredFields()
    {
        Livewire::test(DepartureStandFinderForm::class)
            ->call('submit')
            ->assertHasErrors(['callsign', 'departureAirfield', 'aircraftType']);
    }

    public function testItConvertsDepartureAirfieldToUppercase()
    {
        $stand = Stand::factory()->create([
            'airfield_id' => $this->airfield->id,
            'identifier' => '123',
            'aerodrome_reference_code' => 'C',
            'assignment_priority' => 1,
        ]);

        Livewire::test(DepartureStandFinderForm::class)
            ->set('callsign', 'BAW999')
            ->set('departureAirfield', strtolower($this->icaoCode))
            ->set('aircraftType', $this->aircraft->id)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatched('departureStandFinderFormSubmitted');
    }
}
