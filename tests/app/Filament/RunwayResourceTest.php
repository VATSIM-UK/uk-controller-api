<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\RunwayResource;
use App\Filament\Resources\RunwayResource\Pages\ListRunways;
use App\Models\Runway\Runway;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class RunwayResourceTest extends BaseFilamentTestCase
{
    use ChecksFilamentActionVisibility;
    use ChecksDefaultFilamentAccess;

    public function testItCanFilterForAirfields()
    {
        Livewire::test(ListRunways::class)
            ->assertCanSeeTableRecords([Runway::find(1), Runway::find(2), Runway::find(3)])
            ->filterTable('airfield', 1)
            ->assertCanSeeTableRecords([Runway::find(1), Runway::find(2)])
            ->assertCanNotSeeTableRecords([Runway::find(3)]);
    }

    protected function getCreateText(): string
    {
        return 'Create runway';
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

    protected function resourceClass(): string
    {
        return Runway::class;
    }

    protected function getResourceClass(): string
    {
        return RunwayResource::class;
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
