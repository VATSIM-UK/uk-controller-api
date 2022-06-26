<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\StandResource;
use App\Models\Stand\Stand;
use App\Models\User\User;
use Carbon\Carbon;
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
            ->call('create');

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
            ->call('create');

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
            ->call('create');

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
            ->assertHasErrors(['data.identifier' => 'Stand identifier already in use for airfield.']);
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
            ->assertHasErrors(['data.latitude' => 'numeric']);
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
            ->assertHasErrors(['data.longitude' => 'numeric']);
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
            ->set('data.assigment_priority', 'abc')
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.assigment_priority' => 'numeric']);
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
            ->set('data.assigment_priority', 0)
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.assigment_priority' => 'numeric']);
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
            ->set('data.assigment_priority', 0)
            ->set('data.closed_at', true)
            ->call('create')
            ->assertHasErrors(['data.assigment_priority' => 'numeric']);
    }
}
