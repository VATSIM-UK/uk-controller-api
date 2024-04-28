<?php

namespace App\Filament\Resources\AirfieldResource\Pages;

use App\Filament\Resources\AirfieldResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAirfields extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = AirfieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
