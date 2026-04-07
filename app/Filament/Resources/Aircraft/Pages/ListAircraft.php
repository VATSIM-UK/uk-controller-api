<?php

namespace App\Filament\Resources\Aircraft\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Aircraft\AircraftResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAircraft extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = AircraftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
