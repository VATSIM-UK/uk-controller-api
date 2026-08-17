<?php

namespace App\Filament\Resources\Stands\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\Stands\StandResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStands extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = StandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
