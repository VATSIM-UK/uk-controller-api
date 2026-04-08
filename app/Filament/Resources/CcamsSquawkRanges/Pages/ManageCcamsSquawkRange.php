<?php

namespace App\Filament\Resources\CcamsSquawkRanges\Pages;

use App\Filament\Resources\CcamsSquawkRanges\CcamsSquawkRangeResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Resources\Pages\ManageRecords;

use Filament\Actions\CreateAction;

class ManageCcamsSquawkRange extends ManageRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = CcamsSquawkRangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
