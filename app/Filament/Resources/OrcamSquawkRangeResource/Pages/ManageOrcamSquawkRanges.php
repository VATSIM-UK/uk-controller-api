<?php

namespace App\Filament\Resources\OrcamSquawkRangeResource\Pages;

use App\Filament\Resources\OrcamSquawkRangeResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Resources\Pages\ManageRecords;

class ManageOrcamSquawkRanges extends ManageRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = OrcamSquawkRangeResource::class;
}
