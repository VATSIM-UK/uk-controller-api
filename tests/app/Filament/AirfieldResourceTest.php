<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\AirfieldResource;
use App\Filament\Resources\AirfieldResource\Pages\ListAirfields;
use App\Models\Airfield\Airfield;
use Illuminate\Database\Eloquent\Model;

class AirfieldResourceTest extends BaseFilamentTestCase
{
    use ChecksFilamentActionVisibility;
    use ChecksDefaultFilamentAccess;

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
            'view'
        ];
    }
}
