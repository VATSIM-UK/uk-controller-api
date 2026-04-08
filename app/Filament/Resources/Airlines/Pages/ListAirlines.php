<?php

namespace App\Filament\Resources\Airlines\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Airlines\AirlineResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Resources\Pages\ListRecords;

class ListAirlines extends ListRecords
{
    use LimitsTableRecordListingOptions;

    public static string $resource = AirlineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
