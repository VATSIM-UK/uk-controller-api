<?php

namespace App\Filament\Resources\CcamsSquawkRanges\Pages;

use App\Filament\Resources\CcamsSquawkRanges\CcamsSquawkRangeResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

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
