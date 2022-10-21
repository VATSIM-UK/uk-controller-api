<?php

namespace App\Filament\Resources\CcamsSquawkRangeResource\Pages;

use App\Filament\Resources\CcamsSquawkRangeResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCcamsSquawkRanges extends ListRecords
{
    use LimitsTableRecordListingOptions;
    protected static string $resource = CcamsSquawkRangeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
