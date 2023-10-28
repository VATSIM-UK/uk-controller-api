<?php

namespace App\Filament\Resources\StandAssignmentsHistoryResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\StandAssignmentsHistoryResource;
use Filament\Resources\Pages\ListRecords;

class ListStandAssignmentsHistories extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = StandAssignmentsHistoryResource::class;
}
