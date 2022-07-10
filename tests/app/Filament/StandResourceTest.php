<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\StandResource;
use App\Models\Airfield\Terminal;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

class StandResourceTest extends BaseFilamentTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now()->startOfSecond());
    }

    public function testIndexPageIsForbiddenIfNotAdminUser()
    {
        $this->actingAs(User::factory()->create());
        $this->get(StandResource::getUrl())
            ->assertForbidden();
    }

    public function testItRendersTheIndexPage()
    {
        $this->get(StandResource::getUrl())
            ->assertSuccessful()
            ->assertSeeText('EGLL')
            ->assertSeeText('New stand');
    }

    public function testViewIsForbiddenIfNotAdminUser()
    {
        $this->actingAs(User::factory()->create());
        $this->get(StandResource::getUrl('view', ['record' => Stand::findOrFail(1)]))
            ->assertForbidden();
    }

    public function testItRendersTheViewPage()
    {
        $this->get(StandResource::getUrl('view', ['record' => Stand::findOrFail(1)]))
            ->assertSuccessful()
            ->assertSeeText('View EGLL - 1L');
    }

    public function testCreateIsForbiddenIfNotAdminUser()
    {
        $this->actingAs(User::factory()->create());
        $this->get(StandResource::getUrl('create'))
            ->assertForbidden();
    }

    public function testItRendersTheCreatePage()
    {
        $this->get(StandResource::getUrl('create'))
            ->assertSuccessful()
            ->assertSeeText('Create stand');
    }

    public function testEditIsForbiddenIfNotAdminUser()
    {
        $this->actingAs(User::factory()->create());
        $this->get(StandResource::getUrl('edit', ['record' => Stand::findOrFail(1)]))
            ->assertForbidden();
    }

    public function testItRendersTheEditPage()
    {
        $this->get(StandResource::getUrl('edit', ['record' => Stand::findOrFail(1)]))
            ->assertSuccessful()
            ->assertSeeText('Edit EGLL - 1L');
    }

    public function testItRetrievesDataForView()
    {
        Stand::findOrFail(1)
            ->update(
                [
                    'terminal_id' => 1,
                    'type_id' => 1,
                    'max_aircraft_id' => 1,
                ]
            );

        Livewire::test(StandResource\Pages\ViewStand::class, ['record' => 1])
            ->assertSet('data.airfield_id', 1)
            ->assertSet('data.identifier', '1L')
            ->assertSet('data.latitude', '51.47436111')
            ->assertSet('data.longitude', '-0.48953611')
            ->assertSet('data.terminal_id', 1)
            ->assertSet('data.type_id', 1)
            ->assertSet('data.wake_category_id', 3)
            ->assertSet('data.assignment_priority', 100)
            ->assertSet('data.max_aircraft_id', 1)
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
                    'max_aircraft_id' => 1,
                ]
            );
        Stand::findOrFail(1)->close();

        Livewire::test(StandResource\Pages\ViewStand::class, ['record' => 1])
            ->assertSet('data.airfield_id', 1)
            ->assertSet('data.identifier', '1L')
            ->assertSet('data.latitude', '51.47436111')
            ->assertSet('data.longitude', '-0.48953611')
            ->assertSet('data.terminal_id', 1)
            ->assertSet('data.type_id', 1)
            ->assertSet('data.wake_category_id', 3)
            ->assertSet('data.assignment_priority', 100)
            ->assertSet('data.max_aircraft_id', 1)
            ->assertSet('data.closed_at', false);
    }

    public function testItCreatesAStandWithMinimalData()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 2)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.wake_category_id', 5)
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
                'wake_category_id' => 5,
                'max_aircraft_id' => null,
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
            ->set('data.wake_category_id', 5)
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
                'wake_category_id' => 5,
                'max_aircraft_id' => null,
                'assignment_priority' => 100,
                'closed_at' => Carbon::now(),
            ]
        );
    }

    public function testItCreatesAStandWithAllData()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->set('data.airfield_id', 2)
            ->set('data.terminal_id', 1)
            ->set('data.identifier', '33L')
            ->set('data.latitude', 4.5)
            ->set('data.longitude', 5.6)
            ->set('data.type_id', 3)
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
            ->set('data.assigment_priority', 99)
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'stands',
            [
                'airfield_id' => 2,
                'terminal_id' => 1,
                'identifier' => '33L',
                'latitude' => 4.5,
                'longitude' => 5.6,
                'type_id' => 3,
                'wake_category_id' => 5,
                'max_aircraft_id' => 2,
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
            ->set('data.assignment_priority', 99999)
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.assignment_priority']);
    }

    public function testItRetrievesDataForEdit()
    {
        Stand::findOrFail(1)
            ->update(
                [
                    'terminal_id' => 1,
                    'type_id' => 1,
                    'max_aircraft_id' => 1,
                ]
            );

        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->assertSet('data.airfield_id', 1)
            ->assertSet('data.identifier', '1L')
            ->assertSet('data.latitude', '51.47436111')
            ->assertSet('data.longitude', '-0.48953611')
            ->assertSet('data.terminal_id', 1)
            ->assertSet('data.type_id', 1)
            ->assertSet('data.wake_category_id', 3)
            ->assertSet('data.assignment_priority', 100)
            ->assertSet('data.max_aircraft_id', 1)
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
                    'max_aircraft_id' => 1,
                ]
            );
        Stand::findOrFail(1)->close();

        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->assertSet('data.airfield_id', 1)
            ->assertSet('data.identifier', '1L')
            ->assertSet('data.latitude', '51.47436111')
            ->assertSet('data.longitude', '-0.48953611')
            ->assertSet('data.terminal_id', 1)
            ->assertSet('data.type_id', 1)
            ->assertSet('data.wake_category_id', 3)
            ->assertSet('data.assignment_priority', 100)
            ->assertSet('data.max_aircraft_id', 1)
            ->assertSet('data.closed_at', false);
    }

    public function testItEditsAStandWithMinimalData()
    {
        Livewire::test(StandResource\Pages\EditStand::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.identifier', '33R')
            ->set('data.latitude', 1.2)
            ->set('data.longitude', 3.4)
            ->set('data.wake_category_id', 4)
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
                'wake_category_id' => 4,
                'max_aircraft_id' => null,
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
            ->set('data.wake_category_id', 5)
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
                'wake_category_id' => 5,
                'max_aircraft_id' => null,
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
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
                'wake_category_id' => 5,
                'max_aircraft_id' => 2,
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
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
            ->set('data.wake_category_id', 5)
            ->set('data.max_aircraft_id', 2)
            ->set('data.assignment_priority', 99999)
            ->set('data.closed_at', true)
            ->call('save')
            ->assertHasErrors(['data.assignment_priority']);
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

    public function testItHasWakeCategoryOptions()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->assertSeeHtmlInOrder(
                ['Light (L)', 'Small (S)', 'Lower Medium (LM)', 'Upper Medium (UM)', 'Heavy (H)', 'Jumbo (J)']
            );
    }

    public function testItHasMaxAircraftOptions()
    {
        Livewire::test(StandResource\Pages\CreateStand::class)
            ->assertSeeHtml(
                ['B738', 'A333']
            );
    }

    public function testItListsPairedStands()
    {
        Stand::findOrFail(1)->pairedStands()->sync([2]);
        Livewire::test(
            StandResource\RelationManagers\PairedStandsRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1)]
        )
            ->assertCanSeeTableRecords([Stand::findOrFail(2)]);
    }

    public function testItAllowsStandPairing()
    {
        Livewire::test(
            StandResource\RelationManagers\PairedStandsRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1)]
        )
            ->callTableAction('pair-stand', Stand::findOrFail(1), ['recordId' => 2])
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
            StandResource\RelationManagers\PairedStandsRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1)]
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
            StandResource\RelationManagers\AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1)]
        )
            ->assertCanSeeTableRecords([$rowToExpect]);
    }

    public function testItAllowsAirlinePairingWithMinimalData()
    {
        Livewire::test(
            StandResource\RelationManagers\AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1)]
        )
            ->callTableAction('pair-airline', Stand::findOrFail(1), ['recordId' => 1, 'priority' => 100])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'airline_stand',
            [
                'airline_id' => 1,
                'stand_id' => 1,
                'destination' => null,
                'priority' => 100,
                'callsign_slug' => null,
                'not_before' => null,
            ]
        );
    }

    public function testItAllowsAirlinePairingWithFullData()
    {
        Livewire::test(
            StandResource\RelationManagers\AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1)]
        )
            ->callTableAction(
                'pair-airline',
                Stand::findOrFail(1),
                [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
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
                'callsign_slug' => '1234',
                'not_before' => '20:00:00',
            ]
        );
    }

    public function testItAllowsAirlinesPairedMultipleTimes()
    {
        Stand::findOrFail(1)->airlines()->sync([1]);
        Livewire::test(
            StandResource\RelationManagers\AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1)]
        )
            ->callTableAction(
                'pair-airline',
                Stand::findOrFail(1),
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

    public function testItAllowsAirlineUnpairing()
    {
        Stand::findOrFail(1)->airlines()->sync([3, 2, 1]);
        $rowToUnpair = DB::table('airline_stand')
            ->where('stand_id', 1)
            ->where('airline_id', 3)
            ->first()
            ->id;

        Livewire::test(
            StandResource\RelationManagers\AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1)]
        )
            ->callTableAction('unpair-airline', $rowToUnpair)
            ->assertSuccessful()
            ->assertHasNoTableActionErrors();
        $this->assertEquals([2, 1], Stand::findOrFail(1)->airlines->pluck('id')->toArray());
    }

    public function testItAllowsFailsAirlinePairingPriorityTooLow()
    {
        Livewire::test(
            StandResource\RelationManagers\AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1)]
        )
            ->callTableAction(
                'pair-airline',
                Stand::findOrFail(1),
                [
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
            StandResource\RelationManagers\AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1)]
        )
            ->callTableAction(
                'pair-airline',
                Stand::findOrFail(1),
                [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 99999,
                    'callsign_slug' => '1234',
                    'not_before' => '20:00:00',
                ]
            )->assertHasTableActionErrors(['priority']);
    }

    public function testItAllowsFailsAirlinePairingCallsignTooLong()
    {
        Livewire::test(
            StandResource\RelationManagers\AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1)]
        )
            ->callTableAction(
                'pair-airline',
                Stand::findOrFail(1),
                [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
                    'callsign_slug' => '12345',
                    'not_before' => '20:00:00',
                ]
            )->assertHasTableActionErrors(['callsign_slug']);
    }

    public function testItAllowsFailsAirlinePairingDestinationTooLong()
    {
        Livewire::test(
            StandResource\RelationManagers\AirlinesRelationManager::class,
            ['ownerRecord' => Stand::findOrFail(1)]
        )
            ->callTableAction(
                'pair-airline',
                Stand::findOrFail(1),
                [
                    'recordId' => 1,
                    'destination' => 'EGKKS',
                    'priority' => 55,
                    'callsign_slug' => '1234',
                    'not_before' => '20:00:00',
                ]
            )->assertHasTableActionErrors(['destination']);
    }
}
