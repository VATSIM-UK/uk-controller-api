<?php

namespace App\Filament\Resources\Airfields\Pages;

use App\Filament\Resources\Airfields\AirfieldResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAirfields extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = AirfieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
