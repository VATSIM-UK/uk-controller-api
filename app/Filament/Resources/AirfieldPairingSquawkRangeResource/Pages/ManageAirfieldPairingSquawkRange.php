<?php

namespace App\Filament\Resources\AirfieldPairingSquawkRangeResource\Pages;

use App\Filament\Resources\AirfieldPairingSquawkRangeResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAirfieldPairingSquawkRange extends ManageRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = AirfieldPairingSquawkRangeResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
