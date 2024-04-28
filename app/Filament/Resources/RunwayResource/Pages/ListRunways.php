<?php

namespace App\Filament\Resources\RunwayResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\RunwayResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRunways extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = RunwayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
