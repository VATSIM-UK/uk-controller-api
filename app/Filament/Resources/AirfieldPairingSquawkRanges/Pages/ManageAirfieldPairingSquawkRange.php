<?php

namespace App\Filament\Resources\AirfieldPairingSquawkRanges\Pages;

use App\Filament\Resources\AirfieldPairingSquawkRanges\AirfieldPairingSquawkRangeResource;
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
