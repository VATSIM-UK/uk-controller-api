<?php

namespace App\Filament\Resources\Runways\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\Runways\RunwayResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRunways extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = RunwayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
