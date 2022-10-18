<?php

namespace App\Filament\Resources\SquawkAssignmentResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\SquawkAssignmentResource;
use Filament\Resources\Pages\ListRecords;

class ListSquawkAssignments extends ListRecords
{
    use LimitsTableRecordListingOptions;
    protected static string $resource = SquawkAssignmentResource::class;
}
