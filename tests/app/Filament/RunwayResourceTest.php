<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\RunwayResource;
use App\Filament\Resources\RunwayResource\Pages\EditRunway;
use App\Filament\Resources\RunwayResource\Pages\ListRunways;
use App\Filament\Resources\RunwayResource\Pages\ViewRunway;
use App\Models\Runway\Runway;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class RunwayResourceTest extends BaseFilamentTestCase
{
    use ChecksFilamentActionVisibility;
    use ChecksDefaultFilamentAccess;

    public function testItCanFilterForRunways()
    {
        Livewire::test(ListRunways::class)
            ->assertCanSeeTableRecords([Runway::find(1), Runway::find(2), Runway::find(3)])
            ->filterTable('airfield', 1)
            ->assertCanSeeTableRecords([Runway::find(1), Runway::find(2)])
            ->assertCanNotSeeTableRecords([Runway::find(3)]);
    }

    public function testItLoadsDataForView()
    {
        Livewire::test(ViewRunway::class, ['record' => 1])
            ->assertSet('data.airfield_id', 1)
            ->assertSet('data.identifier', '27L')
            ->assertSet('data.threshold_latitude', 1)
            ->assertSet('data.threshold_longitude', 2)
            ->assertSet('data.heading', 270);
    }

    public function testItCreatesARunway()
    {
        Livewire::test(RunwayResource\Pages\CreateRunway::class)
            ->set('data.airfield_id', 1)
            ->set('data.identifier', '27R')
            ->set('data.threshold_latitude', 4)
            ->set('data.threshold_longitude', 5)
            ->set('data.heading', 271)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'runways',
            [
                'airfield_id' => 1,
                'identifier' => '27R',
                'threshold_latitude' => 4,
                'threshold_longitude' => 5,
                'heading' => 271,
            ]
        );
    }

    public function testItSetsTheInverseRunwayOnCreate()
    {
        Livewire::test(RunwayResource\Pages\CreateRunway::class)
            ->set('data.airfield_id', 2)
            ->set('data.identifier', '15')
            ->set('data.threshold_latitude', 4)
            ->set('data.threshold_longitude', 5)
            ->set('data.heading', 155)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'runway_runway',
            [
                'first_runway_id' => 3,
                'second_runway_id' => Runway::max('id'),
            ]
        );

        $this->assertDatabaseHas(
            'runway_runway',
            [
                'first_runway_id' => Runway::max('id'),
                'second_runway_id' => 3,
            ]
        );
    }

    public function testItDoesntCreateARunwayNoAirfield()
    {
        Livewire::test(RunwayResource\Pages\CreateRunway::class)
            ->set('data.identifier', '27R')
            ->set('data.threshold_latitude', 4)
            ->set('data.threshold_longitude', 5)
            ->set('data.heading', 271)
            ->call('create')
            ->assertHasErrors(['data.airfield_id']);
    }

    public function testItDoesntCreateARunwayNoIdentifier()
    {
        Livewire::test(RunwayResource\Pages\CreateRunway::class)
            ->set('data.airfield_id', 1)
            ->set('data.threshold_latitude', 4)
            ->set('data.threshold_longitude', 5)
            ->set('data.heading', 271)
            ->call('create')
            ->assertHasErrors(['data.identifier']);
    }

    public function testItDoesntCreateARunwayIdentifierInvalid()
    {
        Livewire::test(RunwayResource\Pages\CreateRunway::class)
            ->set('data.airfield_id', 1)
            ->set('data.identifier', 'AAA')
            ->set('data.threshold_latitude', 4)
            ->set('data.threshold_longitude', 5)
            ->set('data.heading', 271)
            ->call('create')
            ->assertHasErrors(['data.identifier']);
    }

    public function testItDoesntCreateARunwayNoLatitude()
    {
        Livewire::test(RunwayResource\Pages\CreateRunway::class)
            ->set('data.airfield_id', 1)
            ->set('data.identifier', '27R')
            ->set('data.threshold_longitude', 5)
            ->set('data.heading', 271)
            ->call('create')
            ->assertHasErrors(['data.threshold_latitude']);
    }

    public function testItDoesntCreateARunwayLatitudeInvalid()
    {
        Livewire::test(RunwayResource\Pages\CreateRunway::class)
            ->set('data.airfield_id', 1)
            ->set('data.identifier', '27R')
            ->set('data.threshold_latitude', 'abc')
            ->set('data.threshold_longitude', 5)
            ->set('data.heading', 271)
            ->call('create')
            ->assertHasErrors(['data.threshold_latitude']);
    }

    public function testItDoesntCreateARunwayNoLongitude()
    {
        Livewire::test(RunwayResource\Pages\CreateRunway::class)
            ->set('data.airfield_id', 1)
            ->set('data.identifier', '27R')
            ->set('data.threshold_latitude', 4)
            ->set('data.heading', 271)
            ->call('create')
            ->assertHasErrors(['data.threshold_longitude']);
    }

    public function testItDoesntCreateARunwayLongitudeInvalid()
    {
        Livewire::test(RunwayResource\Pages\CreateRunway::class)
            ->set('data.airfield_id', 1)
            ->set('data.identifier', '27R')
            ->set('data.threshold_latitude', 4)
            ->set('data.threshold_longitude', 'abc')
            ->set('data.heading', 271)
            ->call('create')
            ->assertHasErrors(['data.threshold_longitude']);
    }

    public function testItDoesntCreateARunwayNoHeading()
    {
        Livewire::test(RunwayResource\Pages\CreateRunway::class)
            ->set('data.airfield_id', 1)
            ->set('data.identifier', '27R')
            ->set('data.threshold_latitude', 4)
            ->set('data.threshold_longitude', 5)
            ->call('create')
            ->assertHasErrors(['data.heading']);
    }

    public function testItDoesntCreateARunwayHeadingInvalid()
    {
        Livewire::test(RunwayResource\Pages\CreateRunway::class)
            ->set('data.airfield_id', 1)
            ->set('data.identifier', '27R')
            ->set('data.threshold_latitude', 4)
            ->set('data.threshold_longitude', 5)
            ->set('data.heading', 2222)
            ->call('create')
            ->assertHasErrors(['data.heading']);
    }

    public function testItLoadsDataForEdit()
    {
        Livewire::test(EditRunway::class, ['record' => 1])
            ->assertSet('data.airfield_id', 1)
            ->assertSet('data.identifier', '27L')
            ->assertSet('data.threshold_latitude', 1)
            ->assertSet('data.threshold_longitude', 2)
            ->assertSet('data.heading', 270);
    }

    public function testItEditARunway()
    {
        Livewire::test(EditRunway::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.identifier', '27R')
            ->set('data.threshold_latitude', 78)
            ->set('data.threshold_longitude', 89)
            ->set('data.heading', 85)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'runways',
            [
                'id' => 1,
                'airfield_id' => 1,
                'identifier' => '27R',
                'threshold_latitude' => 78,
                'threshold_longitude' => 89,
                'heading' => 85,
            ]
        );
    }

    public function testEditingARunwaySetsInverses()
    {
        $runway = Runway::create(
            [
                'airfield_id' => 1,
                'identifier' => '09L',
                'heading' => 91,
                'threshold_latitude' => 1,
                'threshold_longitude' => 2,
            ]
        );

        Livewire::test(EditRunway::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.identifier', '27R')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'runway_runway',
            [
                'first_runway_id' => 1,
                'second_runway_id' => $runway->id,
            ]
        );

        $this->assertDatabaseHas(
            'runway_runway',
            [
                'first_runway_id' => $runway->id,
                'second_runway_id' => 1,
            ]
        );
    }

    public function testItDoesntEditARunwayNoAirfield()
    {
        Livewire::test(EditRunway::class, ['record' => 1])
            ->set('data.airfield_id')
            ->set('data.identifier', '27R')
            ->set('data.threshold_latitude', 4)
            ->set('data.threshold_longitude', 5)
            ->set('data.heading', 271)
            ->call('save')
            ->assertHasErrors(['data.airfield_id']);
    }

    public function testItDoesntEditARunwayNoIdentifier()
    {
        Livewire::test(EditRunway::class, ['record' => 1])
            ->set('data.identifier')
            ->set('data.threshold_latitude', 4)
            ->set('data.threshold_longitude', 5)
            ->set('data.heading', 271)
            ->call('save')
            ->assertHasErrors(['data.identifier']);
    }

    public function testItDoesntEditARunwayIdentifierInvalid()
    {
        Livewire::test(EditRunway::class, ['record' => 1])
            ->set('data.identifier', 'AAA')
            ->set('data.threshold_latitude', 4)
            ->set('data.threshold_longitude', 5)
            ->set('data.heading', 271)
            ->call('save')
            ->assertHasErrors(['data.identifier']);
    }

    public function testItDoesntEditARunwayNoLatitude()
    {
        Livewire::test(EditRunway::class, ['record' => 1])
            ->set('data.identifier', '27R')
            ->set('data.threshold_latitude')
            ->set('data.threshold_longitude', 5)
            ->set('data.heading', 271)
            ->call('save')
            ->assertHasErrors(['data.threshold_latitude']);
    }

    public function testItDoesntEditARunwayLatitudeInvalid()
    {
        Livewire::test(EditRunway::class, ['record' => 1])
            ->set('data.identifier', '27R')
            ->set('data.threshold_latitude', 'abc')
            ->set('data.threshold_longitude', 5)
            ->set('data.heading', 271)
            ->call('save')
            ->assertHasErrors(['data.threshold_latitude']);
    }

    public function testItDoesntEditARunwayNoLongitude()
    {
        Livewire::test(EditRunway::class, ['record' => 1])
            ->set('data.identifier', '27R')
            ->set('data.threshold_longitude')
            ->set('data.threshold_latitude', 4)
            ->set('data.heading', 271)
            ->call('save')
            ->assertHasErrors(['data.threshold_longitude']);
    }

    public function testItDoesntEditARunwayLongitudeInvalid()
    {
        Livewire::test(EditRunway::class, ['record' => 1])
            ->set('data.identifier', '27R')
            ->set('data.threshold_latitude', 4)
            ->set('data.threshold_longitude', 'abc')
            ->set('data.heading', 271)
            ->call('save')
            ->assertHasErrors(['data.threshold_longitude']);
    }

    public function testItDoesntEditARunwayNoHeading()
    {
        Livewire::test(EditRunway::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.identifier', '27R')
            ->set('data.threshold_latitude', 4)
            ->set('data.threshold_longitude', 5)
            ->set('data.heading')
            ->call('save')
            ->assertHasErrors(['data.heading']);
    }

    public function testItDoesntEditARunwayHeadingInvalid()
    {
        Livewire::test(EditRunway::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.identifier', '27R')
            ->set('data.threshold_latitude', 4)
            ->set('data.threshold_longitude', 5)
            ->set('data.heading', 2222)
            ->call('save')
            ->assertHasErrors(['data.heading']);
    }

    protected function getCreateText(): string
    {
        return 'Create Runway';
    }

    protected function getEditRecord(): Model
    {
        return Runway::findOrFail(1);
    }

    protected function getEditText(): string
    {
        return 'Edit 27L';
    }

    protected function getIndexText(): array
    {
        return ['Runways', 'EGLL', '27L', '09R', 'EGBB', '33'];
    }

    protected function getViewText(): string
    {
        return 'View 27L';
    }

    protected function getViewRecord(): Model
    {
        return $this->getEditRecord();
    }

    protected function resourceRecordClass(): string
    {
        return Runway::class;
    }

    protected function resourceId(): int|string
    {
        return 1;
    }

    protected function resourceClass(): string
    {
        return RunwayResource::class;
    }

    protected function writeResourcePageActions(): array
    {
        return [
            'create',
        ];
    }

    protected function resourceListingClass(): string
    {
        return ListRunways::class;
    }

    protected function tableActionRecordClass(): array
    {
        return [];
    }

    protected function tableActionRecordId(): array
    {
        return [];
    }

    protected function writeTableActions(): array
    {
        return [
        ];
    }

    protected function writeResourceTableActions(): array
    {
        return [
        ];
    }

    protected function readOnlyResourceTableActions(): array
    {
        return [
        ];
    }
}
