<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Events\Airline\AirlinesUpdatedEvent;
use App\Filament\Resources\AirlineResource;
use App\Filament\Resources\AirlineResource\Pages\CreateAirline;
use App\Filament\Resources\AirlineResource\Pages\EditAirline;
use App\Filament\Resources\AirlineResource\Pages\ListAirlines;
use App\Filament\Resources\AirlineResource\Pages\ViewAirline;
use App\Filament\Resources\AirlineResource\RelationManagers\StandsRelationManager;
use App\Filament\Resources\AirlineResource\RelationManagers\TerminalsRelationManager;
use App\Models\Airfield\Terminal;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Livewire\Livewire;

class AirlineResourceTest extends BaseFilamentTestCase
{
    use ChecksOperationsContributorActionVisibility;
    use ChecksOperationsContributorAccess;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function testItLoadsDataForView()
    {
        Livewire::test(ViewAirline::class, ['record' => 1])
            ->assertSet('data.icao_code', 'BAW')
            ->assertSet('data.name', 'British Airways')
            ->assertSet('data.callsign', 'SPEEDBIRD')
            ->assertSet('data.is_cargo', false);
    }

    public function testItCreatesAnAirline()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'airlines',
            [
                'icao_code' => 'EZY',
                'name' => 'EasyJet',
                'callsign' => 'EASY',
                'is_cargo' => false,
            ]
        );

        // Check that the event was dispatched
        Event::assertDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItCreatesACargoAirline()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet Cargo')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', true)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'airlines',
            [
                'icao_code' => 'EZY',
                'name' => 'EasyJet Cargo',
                'callsign' => 'EASY',
                'is_cargo' => true,
            ]
        );

        // Check that the event was dispatched
        Event::assertDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItCreatesAnAirlineAndCopiesStandAndTerminalAssignments()
    {
        // Give the airline some terminals and stands
        Airline::findOrFail(1)
            ->stands()
            ->sync([
                1 => [
                    'priority' => 50,
                    'destination' => 'LF',
                    'aircraft_id' => 1,
                    'callsign_slug' => '23',
                    'full_callsign' => 'abc',
                    'not_before' => '09:00:00',
                ],
                2 => [
                    'priority' => 33,
                    'destination' => 'KJ',
                    'callsign_slug' => null,
                    'not_before' => null,
                ]
            ]);

        Airline::findOrFail(1)
            ->terminals()
            ->sync([
                1 => [
                    'priority' => 2,
                    'destination' => 'EB',
                    'callsign_slug' => null,
                ],
                2 => [
                    'priority' => 21,
                    'aircraft_id' => 2,
                    'destination' => 'ED',
                    'callsign_slug' => '55',
                    'full_callsign' => 'def',
                ]
            ]);

        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->set('data.copy_stand_assignments', 1)
            ->call('create')
            ->assertHasNoErrors();

        // Check the stands
        $airline = Airline::where('icao_code', 'EZY')
            ->firstOrFail();

        $this->assertDatabaseHas(
            'airline_stand',
            [
                'airline_id' => $airline->id,
                'stand_id' => 1,
                'priority' => 50,
                'destination' => 'LF',
                'callsign_slug' => '23',
                'full_callsign' => 'abc',
                'not_before' => '09:00:00',
                'aircraft_id' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'airline_stand',
            [
                'airline_id' => $airline->id,
                'stand_id' => 2,
                'priority' => 33,
                'destination' => 'KJ',
                'callsign_slug' => null,
                'full_callsign' => null,
                'not_before' => null,
                'aircraft_id' => null,
            ]
        );

        $this->assertDatabaseHas(
            'airline_terminal',
            [
                'airline_id' => $airline->id,
                'terminal_id' => 1,
                'priority' => 2,
                'aircraft_id' => null,
                'destination' => 'EB',
                'callsign_slug' => null,
                'full_callsign' => null,
            ]
        );

        $this->assertDatabaseHas(
            'airline_terminal',
            [
                'airline_id' => $airline->id,
                'terminal_id' => 2,
                'priority' => 21,
                'aircraft_id' => 2,
                'destination' => 'ED',
                'callsign_slug' => '55',
                'full_callsign' => 'def',
            ]
        );

        // Check that the event was dispatched
        Event::assertDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItCreatesAnAirlineAndDoesntCopyStandAndTerminalAssignments()
    {
        // Give the airline some terminals and stands
        Airline::findOrFail(1)
            ->stands()
            ->sync([
                1 => [
                    'priority' => 50,
                    'destination' => 'LF',
                    'callsign_slug' => '23',
                    'not_before' => '09:00:00',
                ],
                2 => [
                    'priority' => 33,
                    'destination' => 'KJ',
                    'callsign_slug' => null,
                    'not_before' => null,
                ]
            ]);

        Airline::findOrFail(1)
            ->terminals()
            ->sync([
                1 => [
                    'priority' => 2,
                    'destination' => 'EB',
                    'callsign_slug' => null,
                ],
                2 => [
                    'priority' => 21,
                    'destination' => 'ED',
                    'callsign_slug' => '55',
                ]
            ]);

        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasNoErrors();

        // Check the stands
        $airline = Airline::where('icao_code', 'EZY')
            ->firstOrFail();

        $this->assertDatabaseMissing(
            'airline_stand',
            [
                'airline_id' => $airline->id,
            ]
        );

        // Check that the event was dispatched
        Event::assertDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntCreateAnAirlineNoIcaoCode()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.icao_code']);

        // Check that the event was not dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntCreateAnAirlineIcaoCodeEmpty()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', '')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.icao_code']);

        // Check that the event was not dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntCreateAnAirlineIcaoCodeTooLong()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', Str::padRight('', 256, 'a'))
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.icao_code']);

        // Check that the event was not dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntCreateAnAirlineNoName()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.name']);

        // Check that the event was not dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntCreateAnAirlineNameEmpty()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', '')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.name']);

        // Check that the event was not dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntCreateAnAirlineNameTooLong()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', Str::padRight('', 256, 'a'))
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.name']);

        // Check that the event was not dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntCreateAnAirlineNoCallsign()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.callsign']);

        // Check that the event was not dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntCreateAnAirlineCallsignEmpty()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', '')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.callsign']);

        // Check that the event was not dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntCreateAnAirlineCallsignTooLong()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', Str::padRight('', 256, 'a'))
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.callsign']);

        // Check that the event was not dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItLoadsDataForEdit()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->assertSet('data.icao_code', 'BAW')
            ->assertSet('data.name', 'British Airways')
            ->assertSet('data.callsign', 'SPEEDBIRD')
            ->assertSet('data.is_cargo', false);

        // Check that the event was not dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItEditsAnAirline()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'airlines',
            [
                'id' => 1,
                'icao_code' => 'EZY',
                'name' => 'EasyJet',
                'callsign' => 'EASY',
                'is_cargo' => false,
            ]
        );

        // Check that the event was dispatched
        Event::assertDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItEditsACargoAirline()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet Cargo')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'airlines',
            [
                'id' => 1,
                'icao_code' => 'EZY',
                'name' => 'EasyJet Cargo',
                'callsign' => 'EASY',
                'is_cargo' => true,
            ]
        );

        // Check that the event was dispatched
        Event::assertDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntEditAnAirlineNoIcaoCode()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.icao_code']);

        // Check that the event was nmot dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntEditAnAirlineIcaoCodeEmpty()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', '')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.icao_code']);

        // Check that the event was nmot dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntEditAnAirlineIcaoCodeTooLong()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', Str::padRight('', 256, 'a'))
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.icao_code']);

        // Check that the event was nmot dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntEditAnAirlineNoName()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.name']);

        // Check that the event was nmot dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntEditAnAirlineNameEmpty()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name', '')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.name']);

        // Check that the event was nmot dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntEditAnAirlineNameTooLong()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name', Str::padRight('', 256, 'a'))
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.name']);

        // Check that the event was nmot dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntEditAnAirlineNoCallsign()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.callsign']);

        // Check that the event was nmot dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntEditAnAirlineCallsignEmpty()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', '')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.callsign']);

        // Check that the event was nmot dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItDoesntEditAnAirlineCallsignTooLong()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', Str::padRight('', 256, 'a'))
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.callsign']);

        // Check that the event was nmot dispatched
        Event::assertNotDispatched(AirlinesUpdatedEvent::class);
    }

    public function testAirlinesCanBeDeletedFromTheEditPage()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->callAction('delete')
            ->assertHasNoActionErrors();

        $this->assertDatabaseMissing(
            'airlines',
            [
                'id' => 1,
            ]
        );

        // Check that the event was dispatched
        Event::assertDispatched(AirlinesUpdatedEvent::class);
    }

    public function testAirlinesCanBeDeletedFromTheListingPage()
    {
        Livewire::test(ListAirlines::class)
            ->callTableAction('delete', 1)
            ->assertHasNoActionErrors();

        $this->assertDatabaseMissing(
            'airlines',
            [
                'id' => 1,
            ]
        );

        // Check that the event was dispatched
        Event::assertDispatched(AirlinesUpdatedEvent::class);
    }

    public function testItAllowsTerminalPairingWithMinimalData()
    {
        Livewire::test(
            TerminalsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction('pair-terminal', data: ['recordId' => 1, 'priority' => 100])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'airline_terminal',
            [
                'airline_id' => 1,
                'terminal_id' => 1,
                'aircraft_id' => null,
                'destination' => null,
                'priority' => 100,
                'full_callsign' => null,
                'callsign_slug' => null,
            ]
        );
    }

    public function testItAllowsTerminalPairingWithFullData()
    {
        Livewire::test(
            TerminalsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction(
                'pair-terminal',
                data: [
                    'recordId' => 1,
                    'aircraft_id' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
                    'full_callsign' => 'abcd',
                    'callsign_slug' => '1234',
                ]
            )->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'airline_terminal',
            [
                'airline_id' => 1,
                'terminal_id' => 1,
                'aircraft_id' => 1,
                'destination' => 'EGKK',
                'priority' => 55,
                'full_callsign' => 'abcd',
                'callsign_slug' => '1234',
            ]
        );
    }

    public function testItAllowsTerminalsPairedMultipleTimes()
    {
        Terminal::findOrFail(1)->airlines()->sync([1]);
        Livewire::test(
            TerminalsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction(
                'pair-terminal',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
                    'callsign_slug' => '1234',
                ]
            )->assertHasNoTableActionErrors();

        $this->assertDatabaseCount('airline_terminal', 2);
        $this->assertDatabaseHas(
            'airline_terminal',
            [
                'airline_id' => 1,
                'terminal_id' => 1,
                'destination' => 'EGKK',
                'priority' => 55,
                'callsign_slug' => '1234',
            ]
        );
    }

    public function testItAllowsTerminalUnpairing()
    {
        Airline::findOrFail(1)->terminals()->sync([2, 1]);
        $rowToUnpair = DB::table('airline_terminal')
            ->where('terminal_id', 1)
            ->where('airline_id', 1)
            ->first()
            ->id;

        Livewire::test(
            TerminalsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction('unpair-terminal', $rowToUnpair)
            ->assertSuccessful()
            ->assertHasNoTableActionErrors();
        $this->assertEquals([2], Airline::findOrFail(1)->terminals->pluck('id')->sort()->values()->toArray());
    }

    public function testItFailsTerminalPairingPriorityTooLow()
    {
        Livewire::test(
            TerminalsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction(
                'pair-terminal',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => -1,
                    'callsign_slug' => '1234',
                ]
            )->assertHasTableActionErrors(['priority']);
    }

    public function testItFailsTerminalPairingPriorityTooHigh()
    {
        Livewire::test(
            TerminalsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction(
                'pair-terminal',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 99999,
                    'callsign_slug' => '1234',
                ]
            )->assertHasTableActionErrors(['priority']);
    }

    public function testItFailsTerminalPairingCallsignTooLong()
    {
        Livewire::test(
            TerminalsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction(
                'pair-terminal',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
                    'full_callsign' => '12345',
                ]
            )->assertHasTableActionErrors(['full_callsign']);
    }

    public function testItFailsTerminalPairingCallsignSlugTooLong()
    {
        Livewire::test(
            TerminalsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction(
                'pair-terminal',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
                    'callsign_slug' => '12345',
                ]
            )->assertHasTableActionErrors(['callsign_slug']);
    }

    public function testItFailsTerminalPairingDestinationTooLong()
    {
        Livewire::test(
            TerminalsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction(
                'pair-terminal',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKKS',
                    'priority' => 55,
                    'callsign_slug' => '1234',
                ]
            )->assertHasTableActionErrors(['destination']);
    }

    public function testItListsStands()
    {
        Stand::findOrFail(1)->airlines()->sync([1]);
        $rowToExpect = DB::table('airline_stand')->where('airline_id', 1)
            ->where('stand_id', 1)
            ->first()
            ->id;

        Livewire::test(
            StandsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->assertCanSeeTableRecords([$rowToExpect]);
    }

    public function testItAllowsStandPairingWithMinimalData()
    {
        Livewire::test(
            StandsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction('pair-stand', data: ['recordId' => 1, 'priority' => 100])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'airline_stand',
            [
                'airline_id' => 1,
                'stand_id' => 1,
                'destination' => null,
                'priority' => 100,
                'aircraft_id' => null,
                'full_callsign' => null,
                'callsign_slug' => null,
                'not_before' => null,
            ]
        );
    }

    public function testItAllowsStandPairingWithFullData()
    {
        Livewire::test(
            StandsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction(
                'pair-stand',
                data:
                [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
                    'aircraft_id' => 1,
                    'full_callsign' => 'abcd',
                    'callsign_slug' => '1234',
                    'not_before' => '20:00:00',
                ]
            )->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'airline_stand',
            [
                'airline_id' => 1,
                'stand_id' => 1,
                'destination' => 'EGKK',
                'priority' => 55,
                'aircraft_id' => 1,
                'full_callsign' => 'abcd',
                'callsign_slug' => '1234',
                'not_before' => '20:00:00',
            ]
        );
    }

    public function testItAllowsStandsToBePairedMultipleTimes()
    {
        Stand::findOrFail(1)->airlines()->sync([1]);
        Livewire::test(
            StandsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction(
                'pair-stand',
                data:
                [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
                    'callsign_slug' => '1234',
                    'not_before' => '20:00:00',
                ]
            )->assertHasNoTableActionErrors();

        $this->assertDatabaseCount('airline_stand', 2);
        $this->assertDatabaseHas(
            'airline_stand',
            [
                'airline_id' => 1,
                'stand_id' => 1,
                'destination' => 'EGKK',
                'priority' => 55,
                'callsign_slug' => '1234',
                'not_before' => '20:00:00',
            ]
        );
    }

    public function testItAllowsStandUnpairing()
    {
        Airline::findOrFail(1)->stands()->sync([2, 1]);
        $rowToUnpair = DB::table('airline_stand')
            ->where('stand_id', 1)
            ->where('airline_id', 1)
            ->first()
            ->id;

        Livewire::test(
            StandsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction('unpair-stand', $rowToUnpair)
            ->assertSuccessful()
            ->assertHasNoTableActionErrors();
        $this->assertEquals([2], Airline::findOrFail(1)->stands->pluck('id')->sort()->values()->toArray());
    }

    public function testItAllowsFailsStandPairingPriorityTooLow()
    {
        Livewire::test(
            StandsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction(
                'pair-stand',
                data:
                [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => -1,
                    'callsign_slug' => '1234',
                    'not_before' => '20:00:00',
                ]
            )->assertHasTableActionErrors(['priority']);
    }

    public function testItAllowsFailsStandPairingPriorityTooHigh()
    {
        Livewire::test(
            StandsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction(
                'pair-stand',
                data:
                [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 99999,
                    'callsign_slug' => '1234',
                    'not_before' => '20:00:00',
                ]
            )->assertHasTableActionErrors(['priority']);
    }

    public function testItAllowsFailsStandPairingCallsignSlugTooLong()
    {
        Livewire::test(
            StandsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction(
                'pair-stand',
                data:
                [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
                    'callsign_slug' => '12345',
                    'not_before' => '20:00:00',
                ]
            )->assertHasTableActionErrors(['callsign_slug']);
    }

    public function testItAllowsFailsStandPairingCallsignTooLong()
    {
        Livewire::test(
            StandsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction(
                'pair-stand',
                data:
                [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
                    'full_callsign' => '12345',
                    'not_before' => '20:00:00',
                ]
            )->assertHasTableActionErrors(['full_callsign']);
    }

    public function testItAllowsFailsStandPairingDestinationTooLong()
    {
        Livewire::test(
            StandsRelationManager::class,
            ['ownerRecord' => Airline::findOrFail(1)]
        )
            ->callTableAction(
                'pair-stand',
                data:
                [
                    'recordId' => 1,
                    'destination' => 'EGKKS',
                    'priority' => 55,
                    'callsign_slug' => '1234',
                    'not_before' => '20:00:00',
                ]
            )->assertHasTableActionErrors(['destination']);
    }

    protected function getCreateText(): string
    {
        return 'Create Airline';
    }

    protected function getEditRecord(): Model
    {
        return Airline::findOrFail(1);
    }

    protected function getEditText(): string
    {
        return 'Edit BAW';
    }

    protected function getIndexText(): array
    {
        return ['BAW', 'British Airways', 'SPEEDBIRD'];
    }

    protected function getViewText(): string
    {
        return 'View BAW';
    }

    protected function getViewRecord(): Model
    {
        return $this->getEditRecord();
    }

    protected static function resourceClass(): string
    {
        return AirlineResource::class;
    }

    protected static function resourceRecordClass(): string
    {
        return Airline::class;
    }

    protected static function resourceId(): int|string
    {
        return 1;
    }

    protected static function writeResourcePageActions(): array
    {
        return [
            'create',
        ];
    }

    protected static function resourceListingClass(): string
    {
        return ListAirlines::class;
    }

    protected static function tableActionRecordClass(): array
    {
        return [
            TerminalsRelationManager::class => Terminal::class,
            StandsRelationManager::class => Stand::class,
        ];
    }

    protected static function tableActionRecordId(): array
    {
        return [
            TerminalsRelationManager::class => 1,
            StandsRelationManager::class => 1,
        ];
    }

    protected static function writeTableActions(): array
    {
        return [
            TerminalsRelationManager::class => [
                'pair-terminal',
                'unpair-terminal',
                'edit-terminal-pairing',
            ],
            StandsRelationManager::class => [
                'pair-stand',
                'unpair-stand',
                'edit-stand-pairing',
            ],
        ];
    }

    protected static function writeResourceTableActions(): array
    {
        return [
            'edit',
        ];
    }

    protected static function readOnlyResourceTableActions(): array
    {
        return ['view'];
    }
}
