<?php

namespace App\Filament\Resources\AirfieldPairingSquawkRangeResource\Pages;

use App\Filament\Resources\AirfieldPairingSquawkRangeResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Resources\Pages\ManageRecords;

class ManageAirfieldPairingSquawkRange extends ManageRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = AirfieldPairingSquawkRangeResource::class;
}
