<?php

namespace App\Filament\Resources\AircraftResource\Pages;

use App\Filament\Resources\AircraftResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAircraft extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = AircraftResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\EditAction::make(),
        ];
    }
}
