<?php

namespace App\Filament\Resources\CcamsSquawkRangeResource\Pages;

use App\Filament\Resources\CcamsSquawkRangeResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Resources\Pages\ManageRecords;

class ManageCcamsSquawkRange extends ManageRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = CcamsSquawkRangeResource::class;
}
