<?php

namespace App\Filament\Resources\Stands\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\Stands\StandResource;
use Filament\Pages\Actions;
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
