<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\AirfieldResource;
use App\Filament\Resources\AirfieldResource\Pages\CreateAirfield;
use App\Filament\Resources\AirfieldResource\Pages\EditAirfield;
use App\Filament\Resources\AirfieldResource\Pages\ListAirfields;
use App\Models\Airfield\Airfield;
use App\Models\Controller\Handoff;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class AirfieldResourceTest extends BaseFilamentTestCase
{
    use ChecksFilamentActionVisibility;
    use ChecksDefaultFilamentAccess;

    public function testItLoadsDataForView()
    {
        Livewire::test(AirfieldResource\Pages\ViewAirfield::class, ['record' => 1])
            ->assertSet('data.code', 'EGLL')
            ->assertSet('data.latitude', 51.4775)
            ->assertSet('data.longitude', -0.461389)
            ->assertSet('data.elevation', 1)
            ->assertSet('data.wake_category_scheme_id', 1)
            ->assertSet('data.transition_altitude', 6000)
            ->assertSet('data.standard_high', true);
    }

    public function testItCreatesAnAirfield()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'airfield',
            [
                'code' => 'EGKK',
                'latitude' => 12.3,
                'longitude' => 45.6,
                'elevation' => 123,
                'transition_altitude' => 3000,
                'standard_high' => 1,
                'wake_category_scheme_id' => 1,
            ]
        );
    }

    public function testItCreatesADefaultHandoffWithTheAirfield()
    {
        $this->assertFalse(
            Handoff::where('description', 'Default departure handoff for EGKK')->exists()
        );
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasNoErrors();

        $handoff = Handoff::where('description', 'Default departure handoff for EGKK')->firstOrFail();

        $this->assertDatabaseHas(
            'airfield',
            [
                'code' => 'EGKK',
                'latitude' => 12.3,
                'longitude' => 45.6,
                'elevation' => 123,
                'transition_altitude' => 3000,
                'standard_high' => 1,
                'wake_category_scheme_id' => 1,
                'handoff_id' => $handoff->id,
            ]
        );
    }

    public function testItDoesntCreateAirfieldNoIcao()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.code']);
    }

    public function testItDoesntCreateAirfieldInvalidIcao()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGLLLL')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.code']);
    }

    public function testItDoesntCreateAirfieldDuplicateIcao()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGLL')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.code']);
    }

    public function testItDoesntCreateAirfieldNoCoordinates()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.latitude', 'data.longitude']);
    }

    public function testItDoesntCreateAirfieldInvalidCoordinates()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 11111)
            ->set('data.longitude', 1111)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.latitude', 'data.longitude']);
    }

    public function testItDoesntCreateAirfieldNoElevation()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.elevation']);
    }

    public function testItDoesntCreateAirfieldElevationInvalid()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 'abc')
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.elevation']);
    }

    public function testItDoesntCreateAirfieldNoWakeScheme()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.wake_category_scheme_id']);
    }

    public function testItDoesntCreateAirfieldNoTransitionAltitude()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.transition_altitude']);
    }

    public function testItDoesntCreateAirfieldInvalidTransitionAltitude()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 'abc')
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.transition_altitude']);
    }

    public function testItDoesntCreateAirfieldInvalidTransitionAltitudeTooSmall()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', -1)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.transition_altitude']);
    }

    public function testItDoesntCreateAirfieldInvalidTransitionAltitudeTooBig()
    {
        Livewire::test(CreateAirfield::class)
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 20001)
            ->set('data.standard_high', true)
            ->call('create')
            ->assertHasErrors(['data.transition_altitude']);
    }

    public function testItEditsAnAirfield()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.34)
            ->set('data.longitude', 45.67)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'airfield',
            [
                'id' => 1,
                'code' => 'EGKK',
                'latitude' => 12.34,
                'longitude' => 45.67,
                'elevation' => 123,
                'transition_altitude' => 3000,
                'standard_high' => 1,
                'wake_category_scheme_id' => 1,
                'handoff_id' => null,
            ]
        );
    }

    public function testItDoesntEditAirfieldNoIcao()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.code']);
    }

    public function testItDoesntEditAirfieldInvalidIcao()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGLLLL')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.code']);
    }

    public function testItDoesntEditAirfieldNoCoordinates()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude')
            ->set('data.longitude')
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.latitude', 'data.longitude']);
    }

    public function testItDoesntEditAirfieldInvalidCoordinates()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 11111)
            ->set('data.longitude', 1111)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.latitude', 'data.longitude']);
    }

    public function testItDoesntEditAirfieldNoElevation()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation')
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.elevation']);
    }

    public function testItDoesntEditAirfieldElevationInvalid()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 'abc')
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.elevation']);
    }

    public function testItDoesntEditAirfieldNoWakeScheme()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id')
            ->set('data.transition_altitude', 3000)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.wake_category_scheme_id']);
    }

    public function testItDoesntEditAirfieldNoTransitionAltitude()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude')
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.transition_altitude']);
    }

    public function testItDoesntEditAirfieldInvalidTransitionAltitude()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 'abc')
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.transition_altitude']);
    }

    public function testItDoesntEditAirfieldInvalidTransitionAltitudeTooSmall()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', -1)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.transition_altitude']);
    }

    public function testItDoesntEditAirfieldInvalidTransitionAltitudeTooBig()
    {
        Livewire::test(EditAirfield::class, ['record' => 1])
            ->set('data.code', 'EGKK')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->set('data.elevation', 123)
            ->set('data.wake_category_scheme_id', 1)
            ->set('data.transition_altitude', 20001)
            ->set('data.standard_high', true)
            ->call('save')
            ->assertHasErrors(['data.transition_altitude']);
    }

    protected function getCreateText(): string
    {
        return 'Create airfield';
    }

    protected function getEditRecord(): Model
    {
        return Airfield::findOrFail(1);
    }

    protected function getEditText(): string
    {
        return 'Edit EGLL';
    }

    protected function getIndexText(): array
    {
        return ['Airfields', 'EGLL', 'EGBB', 'EGKR'];
    }

    protected function getViewText(): string
    {
        return 'View EGLL';
    }

    protected function getViewRecord(): Model
    {
        return $this->getEditRecord();
    }

    protected function resourceClass(): string
    {
        return Airfield::class;
    }

    protected function getResourceClass(): string
    {
        return AirfieldResource::class;
    }

    protected function resourceId(): int|string
    {
        return 1;
    }

    protected function writeResourcePageActions(): array
    {
        return [
            'create',
        ];
    }

    protected function writeTableActions(): array
    {
        return [];
    }

    protected function readOnlyTableActions(): array
    {
        return [];
    }

    protected function resourceListingClass(): string
    {
        return ListAirfields::class;
    }

    protected function writeResourceTableActions(): array
    {
        return [
            'edit',
        ];
    }

    protected function readOnlyResourceTableActions(): array
    {
        return [
            'view',
        ];
    }
}
