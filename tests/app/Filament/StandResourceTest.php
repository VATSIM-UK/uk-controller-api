<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\StandResource;
use App\Filament\Resources\StandResource\Pages\ListStands;
use App\Filament\Resources\StandResource\RelationManagers\AirlinesRelationManager;
use App\Filament\Resources\StandResource\RelationManagers\PairedStandsRelationManager;
use App\Models\Airfield\Terminal;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use App\Filament\Resources\StandResource\Pages\EditStand;

class StandResourceTest extends BaseFilamentTestCase
{
    use ChecksOperationsContributorAccess;
    use ChecksOperationsContributorActionVisibility;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now()->startOfSecond());
    }

    public function testItCanFilterForAirfieldSpecificStands()
    {
        Livewire::test(ListStands::class)
            ->assertCanSeeTableRecords([Stand::findOrFail(1), Stand::findOrFail(2), Stand::findOrFail(3)])
            ->filterTable('airfield', 1)
            ->assertCanSeeTableRecords([Stand::findOrFail(1), Stand::findOrFail(2)])
            ->assertCanNotSeeTableRecords([Stand::findOrFail(3)]);
    }

    public function testItCanFilterForAirlineSpecificStands()
    {
        Stand::findOrFail(3)->airlines()->sync([1]);

        Livewire::test(ListStands::class)
            ->assertCanSeeTableRecords([Stand::findOrFail(1), Stand::findOrFail(2), Stand::findOrFail(3)])
            ->filterTable('airlines', ['values' => 1])
            ->assertCanSeeTableRecords([Stand::findOrFail(3)])
            ->assertCanNotSeeTableRecords([Stand::findOrFail(1), Stand::findOrFail(2)]);
    }

    public function testItRetrievesDataForView()
    {
        Stand::findOrFail(1)
            ->update(
                [
                    'terminal_id' => 1,
                    'type_id' => 1,
                    'max_aircraft_wingspan' => 1,
                    'max_aircraft_length' => 2,
                    'origin_slug' => 'EGL',
                ]
            );

        Livewire::test(StandResource\Pages\ViewStand::class, ['record' => 1])
            ->assertSet('data.airfield_id', 1)
            ->assertSet('data.identifier', '1L')
            ->assertSet('data.latitude', $this->coordinateEqual('51.47436111'))
            ->assertSet('data.longitude', $this->coordinateEqual('-0.48953611'))
            ->assertSet('data.origin_slug', 'EGL')
            ->assertSet('data.terminal_id', 1)
            ->assertSet('data.type_id', 1)
            ->assertSet('data.aerodrome_reference_code', 'C')
            ->assertSet('data.assignment_priority', 100)
            ->assertSet('data.max_aircraft_wingspan', 1)
            ->assertSet('data.max_aircraft_length', 2)
            ->assertSet('data.closed_at', true);
    }

    public function testItRetrievesDataForViewOfClosedStands()
    {
        Carbon::setTestNow(Carbon::now()->startOfSecond());
        Stand::findOrFail(1)
            ->update(
                [
                    'terminal_id' => 1,
                    'type_id' => 1,
                    'max_aircraft_wingspan' => 1,
                    'max_aircraft_length' => 2,
                ]
            );
        Stand::findOrFail(1)->close();

        Livewire::test(StandResource\Pages\ViewStand::class, ['record' => 1])
            ->assertSet('data.airfield_id', 1)
            ->assertSet('data.identifier', '1L')
            ->assertSet('data.latitude', $this->coordinateEqual('51.47436111'))
            ->assertSet('data.longitude', $this->coordinateEqual('-0.48953611'))
            ->assertSet('data.terminal_id', 1)
            ->assertSet('data.type_id', 1)
            ->assertSet('data.origin_slug', null)
            ->assertSet('data.aerodrome_reference_code', 'C')
            ->assertSet('data.assignment_priority', 100)
            ->assertSet('data.max_aircraft_wingspan', 1)
            ->assertSet('data.max_aircraft_length', 2)
            ->assertSet('data.closed_at', false);
    }

    public function testItCreatesAStandWithMinimalData()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 2)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.aerodrome_reference_code', 'D')
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'stands',
            [
                'airfield_id' => 2,
                'terminal_id' => null,
                'identifier' => '33L',
                'latitude' => 4.5,
                'longitude' => 5.6,
                'type_id' => null,
                'origin_slug' => null,
                'aerodrome_reference_code' => 'D',
                'max_aircraft_wingspan' => null,
                'max_aircraft_length' => null,
                'assignment_priority' => 100,
                'closed_at' => Carbon::now(),
            ]
        );
    }

    public function testItCreatesAStandWithSameIdentifierDifferentAirfield()
    {
        Stand::where('identifier', '1L')->firstOrFail();
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 2)
            ->set('data.identifier', '1L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.aerodrome_reference_code', 'D')
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'stands',
            [
                'airfield_id' => 2,
                'terminal_id' => null,
                'identifier' => '1L',
                'latitude' => 4.5,
                'longitude' => 5.6,
                'type_id' => null,
                'origin_slug' => null,
                'aerodrome_reference_code' => 'D',
                'max_aircraft_wingspan' => null,
                'max_aircraft_length' => null,
                'assignment_priority' => 100,
                'closed_at' => Carbon::now(),
            ]
        );
    }

    public function testItCreatesAStandWithAllData()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 1)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.origin_slug', 'EHA')
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.max_aircraft_wingspan', 1)
            ->set('data.max_aircraft_length', 2)
            ->set('data.assigment_priority', 99)
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'stands',
            [
                'airfield_id' => 1,
                'terminal_id' => 1,
                'identifier' => '33L',
                'latitude' => 4.5,
                'longitude' => 5.6,
                'type_id' => 3,
                'origin_slug' => 'EHA',
                'aerodrome_reference_code' => 'D',
                'max_aircraft_wingspan' => 1,
                'max_aircraft_length' => 2,
                'assignment_priority' => 100,
                'closed_at' => null,
            ]
        );
    }

    public function testCreateFailsWithValidationErrorsIfAirfieldNotSet()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assigment_priority', 99)
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.airfield_id' => 'required']);
    }

    public function testCreateFailsWithValidationErrorsIfIdentifierNotSet()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 2)
            ->set('data.terminal_id', 1)
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assigment_priority', 99)
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.identifier' => 'required']);
    }

    public function testCreateFailsWithValidationErrorsIfIdentifierEmpty()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 2)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assigment_priority', 99)
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.identifier' => 'required']);
    }

    public function testCreateFailsWithValidationErrorsIfIdentifierNotUniqueForAirfield()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 2)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '32')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assigment_priority', 99)
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.identifier']);
    }

    public function testCreateFailsWithValidationErrorsIfLatitudeNonNumeric()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 2)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 'abc')
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assigment_priority', 99)
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.latitude']);
    }

    public function testCreateFailsWithValidationErrorsIfLongitudeNonNumeric()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 2)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 'abc')
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assigment_priority', 99)
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.longitude']);
    }

    public function testCreateFailsWithValidationErrorsIfStandAllocationPriorityNonNumeric()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 2)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assignment_priority', 'abc')
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.assignment_priority']);
    }

    public function testCreateFailsWithValidationErrorsIfStandAllocationPriorityTooSmall()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 2)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assignment_priority', 0)
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.assignment_priority']);
    }

    public function testCreateFailsWithValidationErrorsIfStandAllocationPriorityTooBig()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 2)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assignment_priority', 99999)
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.assignment_priority']);
    }

    public function testCreateFailsWithValidationErrorsIfOriginSlugInvalid()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 2)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.origin_slug', 'EGLLLL')
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assignment_priority', 100)
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.origin_slug']);
    }

    public function testCreateFailsWithValidationErrorsIfWingspanNotNumeric()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 2)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.origin_slug', 'EGLLLL')
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assignment_priority', 100)
            ->set('data.max_aircraft_wingspan', "abc")
            ->set('data.max_aircraft_length', 1)
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.max_aircraft_wingspan']);
    }

    public function testCreateFailsWithValidationErrorsIfLengthNotNumeric()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 2)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.origin_slug', 'EGLLLL')
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assignment_priority', 100)
            ->set('data.max_aircraft_wingspan', 1)
            ->set('data.max_aircraft_length', "abc")
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.max_aircraft_length']);
    }

    public function testItRetrievesDataForEdit()
    {
        Stand::findOrFail(1)
            ->update(
                [
                    'terminal_id' => 1,
                    'type_id' => 1,
                    'max_aircraft_wingspan' => 1,
                    'max_aircraft_length' => 2,
                    'origin_slug' => 'EGGD',
                ]
            );

        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->assertSet('data.airfield_id', 1)
            ->assertSet('data.identifier', '1L')
            ->assertSet('data.latitude', $this->coordinateEqual('51.47436111'))
            ->assertSet('data.longitude', $this->coordinateEqual('-0.48953611'))
            ->assertSet('data.terminal_id', 1)
            ->assertSet('data.type_id', 1)
            ->assertSet('data.origin_slug', 'EGGD')
            ->assertSet('data.aerodrome_reference_code', 'C')
            ->assertSet('data.assignment_priority', 100)
            ->assertSet('data.max_aircraft_wingspan', 1)
            ->assertSet('data.max_aircraft_length', 2)
            ->assertSet('data.closed_at', true);
    }

    public function testItRetrievesDataForEditOfClosedStands()
    {
        Carbon::setTestNow(Carbon::now()->startOfSecond());
        Stand::findOrFail(1)
            ->update(
                [
                    'terminal_id' => 1,
                    'type_id' => 1,
                    'max_aircraft_wingspan' => 1,
                    'max_aircraft_length' => 2,
                    'origin_slug' => 'EGGD',
                ]
            );
        Stand::findOrFail(1)->close();

        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->assertSet('data.airfield_id', 1)
            ->assertSet('data.identifier', '1L')
            ->assertSet('data.latitude', $this->coordinateEqual('51.47436111'))
            ->assertSet('data.longitude', $this->coordinateEqual('-0.48953611'))
            ->assertSet('data.terminal_id', 1)
            ->assertSet('data.type_id', 1)
            ->assertSet('data.origin_slug', 'EGGD')
            ->assertSet('data.aerodrome_reference_code', 'C')
            ->assertSet('data.assignment_priority', 100)
            ->assertSet('data.max_aircraft_wingspan', 1)
            ->assertSet('data.max_aircraft_length', 2)
            ->assertSet('data.closed_at', false);
    }

    public function testItEditsAStandWithMinimalData()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.identifier', '33R')
            ->set('data.latitude', 1.2)
            ->set('data.longitude', 3.4)
            ->set('data.aerodrome_reference_code', 'C')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'stands',
            [
                'id' => 1,
                'airfield_id' => 1,
                'terminal_id' => null,
                'identifier' => '33R',
                'latitude' => 1.2,
                'longitude' => 3.4,
                'type_id' => null,
                'origin_slug' => null,
                'aerodrome_reference_code' => 'C',
                'max_aircraft_wingspan' => null,
                'max_aircraft_length' => null,
                'assignment_priority' => 100,
                'closed_at' => null,
            ]
        );
    }

    public function testItEditsAStandWithSameIdentifierDifferentAirfield()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 3])
            ->set('data.airfield_id', 2)
            ->set('data.identifier', '1L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.aerodrome_reference_code', 'D')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'stands',
            [
                'id' => 3,
                'airfield_id' => 2,
                'terminal_id' => null,
                'identifier' => '1L',
                'latitude' => 4.5,
                'longitude' => 5.6,
                'type_id' => null,
                'origin_slug' => null,
                'aerodrome_reference_code' => 'D',
                'max_aircraft_wingspan' => null,
                'max_aircraft_length' => null,
                'assignment_priority' => 100,
                'closed_at' => null,
            ]
        );
    }

    public function testItEditsAStandWithAllData()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.origin_slug', 'EGGD')
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.max_aircraft_wingspan', 1)
            ->set('data.max_aircraft_length', 2)
            ->set('data.assigment_priority', 99)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'stands',
            [
                'id' => 1,
                'airfield_id' => 1,
                'terminal_id' => 1,
                'identifier' => '33L',
                'latitude' => 4.5,
                'longitude' => 5.6,
                'type_id' => 3,
                'origin_slug' => 'EGGD',
                'aerodrome_reference_code' => 'D',
                'max_aircraft_wingspan' => 1,
                'max_aircraft_length' => 2,
                'assignment_priority' => 100,
                'closed_at' => null,
            ]
        );
    }

    public function testEditFailsWithValidationErrorsIfIdentifierEmpty()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assigment_priority', 99)
            ->set('data.closed_at', true)
            ->call('save')
            ->assertHasErrors(['data.identifier' => 'required']);
    }

    public function testEditFailsWithValidationErrorsIfIdentifierNotUniqueForAirfield()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '251')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assigment_priority', 99)
            ->set('data.closed_at', true)
            ->call('save')
            ->assertHasErrors(['data.identifier']);
    }

    public function testEditFailsWithValidationErrorsIfLatitudeNonNumeric()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 'abc')
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assigment_priority', 99)
            ->set('data.closed_at', true)
            ->call('save')
            ->assertHasErrors(['data.latitude']);
    }

    public function testEditFailsWithValidationErrorsIfLongitudeNonNumeric()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 'abc')
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assigment_priority', 99)
            ->set('data.closed_at', true)
            ->call('save')
            ->assertHasErrors(['data.longitude']);
    }

    public function testEditFailsWithValidationErrorsIfStandAllocationPriorityNonNumeric()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assignment_priority', 'abc')
            ->set('data.closed_at', true)
            ->call('save')
            ->assertHasErrors(['data.assignment_priority']);
    }

    public function testEditFailsWithValidationErrorsIfStandAllocationPriorityTooSmall()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assignment_priority', 0)
            ->set('data.closed_at', true)
            ->call('save')
            ->assertHasErrors(['data.assignment_priority']);
    }

    public function testEditFailsWithValidationErrorsIfStandAllocationPriorityTooBig()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assignment_priority', 99999)
            ->set('data.closed_at', true)
            ->call('save')
            ->assertHasErrors(['data.assignment_priority']);
    }

    public function testEditFailsWithValidationErrorsIfOriginSlugInvalidPartialIcao()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.origin_slug', 'EGGGG')
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assignment_priority', 100)
            ->set('data.closed_at', true)
            ->call('save')
            ->assertHasErrors(['data.origin_slug']);
    }

    public function testEditFailsWithValidationErrorsIfMaxAircraftLengthNotNumeric()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.origin_slug', 'EGGGG')
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assignment_priority', 100)
            ->set('data.max_aircraft_length', "abc")
            ->set('data.max_aircraft_wingspan', 1)
            ->set('data.closed_at', true)
            ->call('save')
            ->assertHasErrors(['data.max_aircraft_length']);
    }

    public function testEditFailsWithValidationErrorsIfMaxAircraftWingspanNotNumeric()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.origin_slug', 'EGGGG')
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assignment_priority', 100)
            ->set('data.max_aircraft_length', 1)
            ->set('data.max_aircraft_wingspan', "abc")
            ->set('data.closed_at', true)
            ->call('save')
            ->assertHasErrors(['data.max_aircraft_wingspan']);
    }

    public function testEditFailsWithValidationErrorsIfMaxAircraftLengthNegative()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.origin_slug', 'EGGGG')
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assignment_priority', 100)
            ->set('data.max_aircraft_length', -1)
            ->set('data.max_aircraft_wingspan', 1)
            ->set('data.closed_at', true)
            ->call('save')
            ->assertHasErrors(['data.max_aircraft_length']);
    }

    public function testEditFailsWithValidationErrorsIfMaxAircraftWingspanNegative()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.origin_slug', 'EGGGG')
            ->set('data.aerodrome_reference_code', 'D')
            ->set('data.assignment_priority', 100)
            ->set('data.max_aircraft_length', 1)
            ->set('data.max_aircraft_wingspan', -1)
            ->set('data.closed_at', true)
            ->call('save')
            ->assertHasErrors(['data.max_aircraft_wingspan']);
    }

    public function testItOnlyAllowsSelectionOfTerminalsAtTheRightAirfield()
    {
        $terminal = Terminal::factory()->create(['airfield_id' => 2]);
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 1)
            ->assertSeeHtmlInOrder(['Terminal 1', 'Terminal 2'])
            ->assertDontSeeHtml($terminal->description)
            ->set('data.terminal_id', 1)
            ->set('data.airfield_id', 2)
            ->assertDontSeeHtml(['Terminal 1', 'Terminal 2'])
            ->assertSet('data.terminal_id', null)
            ->assertSeeHtml([$terminal->description]);
    }

    public function testItHasAllAirfieldsForCreatingStands()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->assertSeeHtml(['EGBB', 'EGKR', 'EGLL']);
    }

    public function testItHasStandTypeOptions()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->assertSeeHtml(['Domestic', 'International', 'Cargo']);
    }

    public function testItHasAerodromeReferenceCodeOptions()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->assertSeeHtmlInOrder(
                ['A', 'B', 'C', 'D', 'E', 'F']
            );
    }

    public function testItListsPairedStands()
    {
        Stand::findOrFail(1)->pairedStands()->sync([2]);
        Livewire::test(
            PairedStandsRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1), 'pageClass' => EditStand::class]
        )
            ->assertCanSeeTableRecords([Stand::findOrFail(2)]);
    }

    public function testItAllowsStandPairing()
    {
        Livewire::test(
            PairedStandsRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1), 'pageClass' => EditStand::class]
        )
            ->callTableAction('pair-stand', data: ['recordId' => 2])
            ->assertSuccessful()
            ->assertHasNoTableActionErrors();
        $this->assertEquals([2], Stand::findOrFail(1)->pairedStands->pluck('id')->toArray());
        $this->assertEquals([1], Stand::findOrFail(2)->pairedStands->pluck('id')->toArray());
    }

    public function testItAllowsStandUnpairing()
    {
        Stand::findOrFail(1)->pairedStands()->sync([2, 3]);
        Stand::findOrFail(2)->pairedStands()->sync([3, 1]);
        Stand::findOrFail(3)->pairedStands()->sync([1, 2]);
        Livewire::test(
            PairedStandsRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1), 'pageClass' => EditStand::class]
        )
            ->callTableAction('unpair-stand', 2)
            ->assertSuccessful()
            ->assertHasNoTableActionErrors();
        $this->assertEquals([3], Stand::findOrFail(1)->pairedStands->pluck('id')->toArray());
        $this->assertEquals([3], Stand::findOrFail(2)->pairedStands->pluck('id')->toArray());
    }

    public function testItListsAirlines()
    {
        Stand::findOrFail(1)->airlines()->sync([1]);
        $rowToExpect = DB::table('airline_stand')->where('airline_id', 1)
            ->where('stand_id', 1)
            ->first()
            ->id;

        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1), 'pageClass' => EditStand::class]
        )
            ->assertCanSeeTableRecords([$rowToExpect]);
    }

    public function testItAllowsAirlinePairingWithMinimalData()
    {
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1), 'pageClass' => EditStand::class]
        )
            ->callTableAction('pair-airline', data: ['recordId' => 1, 'priority' => 100])
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

    public function testItAllowsAirlinePairingWithFullData()
    {
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1), 'pageClass' => EditStand::class]
        )
            ->callTableAction(
                'pair-airline',
                data: [
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

    public function testItAllowsAirlinesPairedMultipleTimes()
    {
        Stand::findOrFail(1)->airlines()->sync([1]);
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1), 'pageClass' => EditStand::class]
        )
            ->callTableAction(
                'pair-airline',
                data: [
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

    public function testItAllowsAirlineUnpairing()
    {
        Stand::findOrFail(1)->airlines()->sync([3, 2, 1]);
        $rowToUnpair = DB::table('airline_stand')
            ->where('stand_id', 1)
            ->where('airline_id', 3)
            ->first()
            ->id;

        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1), 'pageClass' => EditStand::class]
        )
            ->callTableAction('unpair-airline', $rowToUnpair)
            ->assertSuccessful()
            ->assertHasNoTableActionErrors();
        $this->assertEquals([1, 2], Stand::findOrFail(1)->airlines->pluck('id')->sort()->values()->toArray());
    }

    public function testItAllowsFailsAirlinePairingPriorityTooLow()
    {
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1), 'pageClass' => EditStand::class]
        )
            ->callTableAction(
                'pair-airline',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => -1,
                    'callsign_slug' => '1234',
                    'not_before' => '20:00:00',
                ]
            )->assertHasTableActionErrors(['priority']);
    }

    public function testItAllowsFailsAirlinePairingPriorityTooHigh()
    {
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1), 'pageClass' => EditStand::class]
        )
            ->callTableAction(
                'pair-airline',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 99999,
                    'callsign_slug' => '1234',
                    'not_before' => '20:00:00',
                ]
            )->assertHasTableActionErrors(['priority']);
    }

    public function testItAllowsFailsAirlinePairingCallsignSlugTooLong()
    {
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1), 'pageClass' => EditStand::class]
        )
            ->callTableAction(
                'pair-airline',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
                    'callsign_slug' => '12345',
                    'not_before' => '20:00:00',
                ]
            )->assertHasTableActionErrors(['callsign_slug']);
    }

    public function testItAllowsFailsAirlinePairingCallsignTooLong()
    {
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1), 'pageClass' => EditStand::class]
        )
            ->callTableAction(
                'pair-airline',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
                    'full_callsign' => '12345',
                    'not_before' => '20:00:00',
                ]
            )->assertHasTableActionErrors(['full_callsign']);
    }

    public function testItAllowsFailsAirlinePairingDestinationTooLong()
    {
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1), 'pageClass' => EditStand::class]
        )
            ->callTableAction(
                'pair-airline',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKKS',
                    'priority' => 55,
                    'callsign_slug' => '1234',
                    'not_before' => '20:00:00',
                ]
            )->assertHasTableActionErrors(['destination']);
    }

    protected static function resourceClass(): string
    {
        return StandResource::class;
    }

    protected function getEditText(): string
    {
        return '1L';
    }

    protected function getCreateText(): string
    {
        return 'Create Stand';
    }

    protected function getViewText(): string
    {
        return '1L';
    }

    protected function getIndexText(): array
    {
        return ['EGLL', 'EGBB'];
    }

    protected static function resourceId(): int|string
    {
        return 1;
    }

    protected static function resourceRecordClass(): string
    {
        return Stand::class;
    }

    protected static function resourceListingClass(): string
    {
        return ListStands::class;
    }

    protected static function writeResourceTableActions(): array
    {
        return [
            'edit',
        ];
    }

    protected static function readOnlyResourceTableActions(): array
    {
        return [
            'view',
        ];
    }

    protected static function writeResourcePageActions(): array
    {
        return [
            'create',
        ];
    }

    protected static function tableActionRecordClass(): array
    {
        return [
            PairedStandsRelationManager::class => Stand::class,
            AirlinesRelationManager::class => Airline::class,
        ];
    }

    protected static function tableActionRecordId(): array
    {
        return [
            PairedStandsRelationManager::class => 1,
            AirlinesRelationManager::class => 1,
        ];
    }

    protected static function writeTableActions(): array
    {
        return [
            PairedStandsRelationManager::class => [
                'pair-stand',
                'unpair-stand',
            ],
            AirlinesRelationManager::class => [
                'pair-airline',
                'unpair-airline',
                'edit-airline-pairing',
            ],
        ];
    }

    protected function getEditRecord(): Model
    {
        return Stand::find(1);
    }

    protected function getViewRecord(): Model
    {
        return Stand::find(1);
    }

    private function coordinateEqual(string $expected): callable
    {
        return fn(float $actual) => number_format($actual, 8) === $expected;
    }
}
