<?php

namespace App\Filament\Resources\UnitDiscreteSquawkRangeResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\UnitDiscreteSquawkRangeResource;
use Filament\Resources\Pages\ManageRecords;

class ManageUnitDiscreteSquawkRanges extends ManageRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = UnitDiscreteSquawkRangeResource::class;
}
