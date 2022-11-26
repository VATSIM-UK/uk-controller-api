<?php

namespace App\Filament\Resources\NonAssignableSquawkCodeResource\Pages;

use App\Filament\Resources\NonAssignableSquawkCodeResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Resources\Pages\ManageRecords;

class ManageNonAssignnableSquawkCodeRanges extends ManageRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = NonAssignableSquawkCodeResource::class;
}
