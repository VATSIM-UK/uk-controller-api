<?php

namespace App\Filament\Resources\UnitDiscreteSquawkRangeGuestResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\UnitDiscreteSquawkRangeGuestResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\CreateAction;

class ManageUnitDiscreteSquawkRangeGuests extends ManageRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = UnitDiscreteSquawkRangeGuestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
