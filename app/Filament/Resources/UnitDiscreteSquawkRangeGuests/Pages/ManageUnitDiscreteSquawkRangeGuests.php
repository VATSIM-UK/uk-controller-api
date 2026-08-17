<?php

namespace App\Filament\Resources\UnitDiscreteSquawkRangeGuests\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\UnitDiscreteSquawkRangeGuests\UnitDiscreteSquawkRangeGuestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

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
