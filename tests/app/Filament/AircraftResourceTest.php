<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\AircraftResource;
use App\Filament\Resources\AircraftResource\Pages\CreateAircraft;
use App\Filament\Resources\AircraftResource\Pages\EditAircraft;
use App\Filament\Resources\AircraftResource\Pages\ViewAircraft;
use App\Models\Aircraft\Aircraft;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class AircraftResourceTest extends BaseFilamentTestCase
{
    use ChecksOperationsContributorActionVisibility;
    use ChecksOperationsContributorAccess;

    public function testItLoadsDataForView()
    {
        Livewire::test(ViewAircraft::class, ['record' => 1])
            ->assertSet('data.code', 'B738')
            ->assertSet('data.aerodrome_reference_code', 'C')
            ->assertSet('data.wingspan', 117.83)
            ->assertSet('data.length', 129.50)
            ->assertSet('data.allocate_stands', true);
    }

    public function testItCreatesAnAircraft()
    {
        Livewire::test(CreateAircraft::class)
            ->set('data.code', 'A346')
            ->set('data.aerodrome_reference_code', 'E')
            ->set('data.wingspan', 197.83)
            ->set('data.length', 208.99)
            ->set('data.allocate_stands', true)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('aircraft', [
            'code' => 'A346',
            'aerodrome_reference_code' => 'E',
            'wingspan' => 197.83,
            'length' => 208.99,
            'allocate_stands' => true,
        ]);
    }

    public function testItDoesntCreateAnAircraftWithNoCode()
    {
        Livewire::test(CreateAircraft::class)
            ->set('data.aerodrome_reference_code', 'E')
            ->set('data.wingspan', 197.83)
            ->set('data.length', 208.99)
            ->set('data.allocate_stands', true)
            ->call('create')
            ->assertHasErrors('data.code');
    }

    public function testItDoesntCreateAnAircraftWithEmptyCode()
    {
        Livewire::test(CreateAircraft::class)
            ->set('data.code', '')
            ->set('data.aerodrome_reference_code', 'E')
            ->set('data.wingspan', 197.83)
            ->set('data.length', 208.99)
            ->set('data.allocate_stands', true)
            ->call('create')
            ->assertHasErrors('data.code');
    }

    public function testItDoesntCreateAnAircraftWithClashingCode()
    {
        Livewire::test(CreateAircraft::class)
            ->set('data.code', 'B738')
            ->set('data.aerodrome_reference_code', 'E')
            ->set('data.wingspan', 197.83)
            ->set('data.length', 208.99)
            ->set('data.allocate_stands', true)
            ->call('create')
            ->assertHasErrors('data.code');
    }

    public function testItDoesntCreateAnAircraftWithNoAerodromeReferenceCode()
    {
        Livewire::test(CreateAircraft::class)
            ->set('data.code', 'A346')
            ->set('data.wingspan', 197.83)
            ->set('data.length', 208.99)
            ->set('data.allocate_stands', true)
            ->call('create')
            ->assertHasErrors('data.aerodrome_reference_code');
    }

    public function testItDoesntCreateAnAircraftWithNoWingspan()
    {
        Livewire::test(CreateAircraft::class)
            ->set('data.code', 'A346')
            ->set('data.aerodrome_reference_code', 'E')
            ->set('data.length', 208.99)
            ->set('data.allocate_stands', true)
            ->call('create')
            ->assertHasErrors('data.wingspan');
    }

    public function testItDoesntCreateAnAircraftWithNegativeWingspan()
    {
        Livewire::test(CreateAircraft::class)
            ->set('data.code', 'A346')
            ->set('data.aerodrome_reference_code', 'E')
            ->set('data.wingspan', -197.83)
            ->set('data.length', 208.99)
            ->set('data.allocate_stands', true)
            ->call('create')
            ->assertHasErrors('data.wingspan');
    }

    public function testItDoesntCreateAnAircraftWithNoLength()
    {
        Livewire::test(CreateAircraft::class)
            ->set('data.code', 'A346')
            ->set('data.aerodrome_reference_code', 'E')
            ->set('data.wingspan', 197.83)
            ->set('data.allocate_stands', true)
            ->call('create')
            ->assertHasErrors('data.length');
    }

    public function testItDoesntCreateAnAircraftWithNegativeLength()
    {
        Livewire::test(CreateAircraft::class)
            ->set('data.code', 'A346')
            ->set('data.aerodrome_reference_code', 'E')
            ->set('data.wingspan', 197.83)
            ->set('data.length', -208.99)
            ->set('data.allocate_stands', true)
            ->call('create')
            ->assertHasErrors('data.length');
    }

    public function testItLoadsDataForEdit()
    {
        Livewire::test(EditAircraft::class, ['record' => 1])
            ->assertSet('data.code', 'B738')
            ->assertSet('data.aerodrome_reference_code', 'C')
            ->assertSet('data.wingspan', 117.83)
            ->assertSet('data.length', 129.50)
            ->assertSet('data.allocate_stands', true);
    }

    public function testItEditsAnAircraft()
    {
        Livewire::test(EditAircraft::class, ['record' => 1])
            ->set('data.code', 'B739')
            ->set('data.aerodrome_reference_code', 'F')
            ->set('data.wingspan', 117.83)
            ->set('data.length', 129.50)
            ->set('data.allocate_stands', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('aircraft', [
            'id' => 1,
            'code' => 'B739',
            'aerodrome_reference_code' => 'F',
            'wingspan' => 117.83,
            'length' => 129.50,
            'allocate_stands' => false,
        ]);
    }

    public function testItEditsAnAircraftAndDoesntErrorWithExistingCode()
    {
        Livewire::test(EditAircraft::class, ['record' => 1])
            ->set('data.code', 'B738')
            ->set('data.aerodrome_reference_code', 'F')
            ->set('data.wingspan', 117.83)
            ->set('data.length', 129.50)
            ->set('data.allocate_stands', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('aircraft', [
            'id' => 1,
            'code' => 'B738',
            'aerodrome_reference_code' => 'F',
            'wingspan' => 117.83,
            'length' => 129.50,
            'allocate_stands' => false,
        ]);
    }

    public function testItDoesntEditAnAircraftWithNoCode()
    {
        Livewire::test(EditAircraft::class, ['record' => 1])
            ->set('data.code')
            ->set('data.aerodrome_reference_code', 'F')
            ->set('data.wingspan', 117.83)
            ->set('data.length', 129.50)
            ->set('data.allocate_stands', false)
            ->call('save')
            ->assertHasErrors('data.code');
    }

    public function testItDoesntEditAnAircraftWithEmptyCode()
    {
        Livewire::test(EditAircraft::class, ['record' => 1])
            ->set('data.code', '')
            ->set('data.aerodrome_reference_code', 'F')
            ->set('data.wingspan', 117.83)
            ->set('data.length', 129.50)
            ->set('data.allocate_stands', false)
            ->call('save')
            ->assertHasErrors('data.code');
    }

    public function testItDoesntEditAnAircraftWithClashingCode()
    {
        Livewire::test(EditAircraft::class, ['record' => 1])
            ->set('data.code', 'A333')
            ->set('data.aerodrome_reference_code', 'F')
            ->set('data.wingspan', 117.83)
            ->set('data.length', 129.50)
            ->set('data.allocate_stands', false)
            ->call('save')
            ->assertHasErrors('data.code');
    }

    public function testItDoesntEditAnAircraftWithNoWingspan
    ()
    {
        Livewire::test(EditAircraft::class, ['record' => 1])
            ->set('data.code', 'B738')
            ->set('data.aerodrome_reference_code', 'F')
            ->set('data.wingspan')
            ->set('data.length', 129.50)
            ->set('data.allocate_stands', false)
            ->call('save')
            ->assertHasErrors('data.wingspan');
    }

    public function testItDoesntEditAnAircraftWithNegativeWingspan()
    {
        Livewire::test(EditAircraft::class, ['record' => 1])
            ->set('data.code', 'B738')
            ->set('data.aerodrome_reference_code', 'F')
            ->set('data.wingspan', -117.83)
            ->set('data.length', 129.50)
            ->set('data.allocate_stands', false)
            ->call('save')
            ->assertHasErrors('data.wingspan');
    }

    public function testItDoesntEditAnAircraftWithNoLength()
    {
        Livewire::test(EditAircraft::class, ['record' => 1])
            ->set('data.code', 'B738')
            ->set('data.aerodrome_reference_code', 'F')
            ->set('data.wingspan', 117.83)
            ->set('data.length')
            ->set('data.allocate_stands', false)
            ->call('save')
            ->assertHasErrors('data.length');
    }

    public function testItDoesntEditAnAircraftWithNegativeLength()
    {
        Livewire::test(EditAircraft::class, ['record' => 1])
            ->set('data.code', 'B738')
            ->set('data.aerodrome_reference_code', 'F')
            ->set('data.wingspan', 117.83)
            ->set('data.length', -129.50)
            ->set('data.allocate_stands', false)
            ->call('save')
            ->assertHasErrors('data.length');
    }

    protected function getCreateText(): string
    {
        return 'Create Aircraft';
    }

    protected function getEditRecord(): Model
    {
        return Aircraft::find(1);
    }

    protected function getEditText(): string
    {
        return 'Edit B738';
    }

    protected function getIndexText(): array
    {
        return ['B738'];
    }

    protected function getViewText(): string
    {
        return 'View B738';
    }

    protected function getViewRecord(): Model
    {
        return Aircraft::find(1);
    }

    protected function resourceClass(): string
    {
        return AircraftResource::class;
    }
}
