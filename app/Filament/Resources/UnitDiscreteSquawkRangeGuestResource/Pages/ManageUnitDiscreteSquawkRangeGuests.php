<?php

namespace App\Filament\Resources\UnitDiscreteSquawkRangeGuestResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\UnitDiscreteSquawkRangeGuestResource;
use Filament\Resources\Pages\ManageRecords;

class ManageUnitDiscreteSquawkRangeGuests extends ManageRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = UnitDiscreteSquawkRangeGuestResource::class;
}
