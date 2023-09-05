<?php

namespace App\Http\Livewire;

use App\BaseFilamentTestCase;
use Livewire\Livewire;

class StandPredictorFormTest extends BaseFilamentTestCase
{
    public function testItRenders()
    {
        Livewire::test(StandPredictorForm::class)
            ->assertOk();
    }

    public function testItSubmits()
    {
        Livewire::test(StandPredictorForm::class)
            ->set('callsign', 'BAW999')
            ->set('aircraftType', 1)
            ->set('departureAirfield', 'EGKK')
            ->set('arrivalAirfield', 'EGLL')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertEmitted('standPredictorFormSubmitted', [
                'callsign' => 'BAW999',
                'cid' => 1203533,
                'aircraft_id' => 1,
                'airline_id' => 1,
                'planned_depairport' => 'EGKK',
                'planned_destairport' => 'EGLL',
            ]);
    }

    public function testItDoesntSubmitIfNoCallsign()
    {
        Livewire::test(StandPredictorForm::class)
            ->set('aircraftType', 1)
            ->set('departureAirfield', 'EGKK')
            ->set('arrivalAirfield', 'EGLL')
            ->call('submit')
            ->assertHasErrors(['callsign'])
            ->assertNotEmitted('standPredictorFormSubmitted');
    }

    public function testItDoesntSubmitIfNoAircraftType()
    {
        Livewire::test(StandPredictorForm::class)
            ->set('callsign', 'BAW123')
            ->set('departureAirfield', 'EGKK')
            ->set('arrivalAirfield', 'EGLL')
            ->call('submit')
            ->assertHasErrors(['aircraftType'])
            ->assertNotEmitted('standPredictorFormSubmitted');
    }

    public function testItDoesntSubmitIfNoDepartureAirfield()
    {
        Livewire::test(StandPredictorForm::class)
            ->set('callsign', 'BAW123')
            ->set('arrivalAirfield', 'EGLL')
            ->call('submit')
            ->assertHasErrors(['departureAirfield'])
            ->assertNotEmitted('standPredictorFormSubmitted');
    }

    public function testItDoesntSubmitIfNoArrivalAirfield()
    {
        Livewire::test(StandPredictorForm::class)
            ->set('callsign', 'BAW123')
            ->set('departureAirfield', 'EGKK')
            ->call('submit')
            ->assertHasErrors(['arrivalAirfield'])
            ->assertNotEmitted('standPredictorFormSubmitted');
    }
}
