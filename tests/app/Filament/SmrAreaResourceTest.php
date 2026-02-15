<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\SmrAreaResource;
use App\Filament\Resources\SmrAreaResource\Pages\CreateSmrArea;
use App\Filament\Resources\SmrAreaResource\Pages\EditSmrArea;
use App\Filament\Resources\SmrAreaResource\Pages\ListSmrAreas;
use App\Filament\Resources\SmrAreaResource\Pages\ViewSmrArea;
use App\Models\SmrArea;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class SmrAreaResourceTest extends BaseFilamentTestCase
{
    use ChecksOperationsContributorActionVisibility;
    use ChecksOperationsContributorAccess;

    private const TEST_COORDINATE = "COORD:N000.00.00.000:E000.00.00.000\n";

    public function testItLoadsDataForView()
    {
        Livewire::test(ViewSmrArea::class, ['record' => 1])
            ->assertSet('data.airfield_id', 1);
    }

    public function testItCreatesAnArea()
    {
        Livewire::test(CreateSmrArea::class)
            ->set('data.airfield_id', 2)
            ->set('data.coordinates', str_repeat($this::TEST_COORDINATE, 3))
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('smr_area', ['airfield_id' => 2]);
    }

    public function testItDoesntCreateAnAreaNoAirfieldId()
    {
        Livewire::test(CreateSmrArea::class)
            ->set('data.coordinates', str_repeat($this::TEST_COORDINATE, 3))
            ->call('create')
            ->assertHasErrors(['data.airfield_id']);
    }

    public function testItDoesntCreateAnAreaNoCoordinates()
    {
        Livewire::test(CreateSmrArea::class)
            ->set('data.airfield_id', 1)
            ->call('create')
            ->assertHasErrors(['data.coordinates']);
    }

    public function testItDoesntCreateAnAreaTooFewCoordinates()
    {
        Livewire::test(CreateSmrArea::class)
            ->set('data.airfield_id', 1)
            ->set('data.coordinates', $this::TEST_COORDINATE)
            ->call('create')
            ->assertHasErrors(['data.coordinates']);
    }

    public function testItDoesntCreateAnAreaInvalidCoordinates()
    {
        Livewire::test(CreateSmrArea::class)
            ->set('data.airfield_id', 1)
            ->set('data.coordinates', str_repeat("COORD:INVALID\n", 3))
            ->call('create')
            ->assertHasErrors(['data.coordinates']);
    }

    public function testItLoadsDataForEdit()
    {
        Livewire::test(EditSmrArea::class, ['record' => 1])
            ->assertSet('data.airfield_id', 1);
    }

    public function testItEditsAnArea()
    {
        Livewire::test(EditSmrArea::class, ['record' => 1])
            ->set('data.airfield_id', 3)
            ->set('data.source', 'Some source')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'smr_area',
            [
                'id' => 1,
                'airfield_id' => 3,
                'source' => 'Some source',
            ]
        );
    }

    public function testItDoesntEditAnAreaNoAirfieldId()
    {
        Livewire::test(EditSmrArea::class, ['record' => 1])
            ->set('data.airfield_id')
            ->set('data.coordinates', str_repeat($this::TEST_COORDINATE, 3))
            ->call('save')
            ->assertHasErrors(['data.airfield_id']);
    }

    public function testItDoesntEditAnAreaNoCoordinates()
    {
        Livewire::test(EditSmrArea::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.coordinates')
            ->call('save')
            ->assertHasErrors(['data.coordinates']);
    }

    public function testItDoesntEditAnAreaTooFewCoordinates()
    {
        Livewire::test(EditSmrArea::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.coordinates', $this::TEST_COORDINATE)
            ->call('save')
            ->assertHasErrors(['data.coordinates']);
    }

    public function testItDoesntEditAnAreaInvalidCoordinates()
    {
        Livewire::test(EditSmrArea::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.coordinates', str_repeat("COORD:INVALID\n", 3))
            ->call('save')
            ->assertHasErrors(['data.coordinates']);
    }

    protected function getCreateText(): string
    {
        return 'Create SMR Area';
    }

    protected function getEditRecord(): Model
    {
        return SmrArea::findOrFail(1);
    }

    protected function getEditText(): string
    {
        return 'Edit SMR Area';
    }

    protected function getIndexText(): array
    {
        return ['SMR Areas'];
    }

    protected function getViewText(): string
    {
        return 'View SMR Area';
    }

    protected function getViewRecord(): Model
    {
        return $this->getEditRecord();
    }

    protected static function resourceClass(): string
    {
        return SmrAreaResource::class;
    }

    protected static function resourceRecordClass(): string
    {
        return SmrArea::class;
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
        return ListSmrAreas::class;
    }

    protected static function writeResourceTableActions(): array
    {
        return [
            'edit',
            'delete',
        ];
    }
}
