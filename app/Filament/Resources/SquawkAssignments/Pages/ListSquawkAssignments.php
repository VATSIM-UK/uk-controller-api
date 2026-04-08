<?php

namespace App\Filament\Resources\SquawkAssignments\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\SquawkAssignments\SquawkAssignmentResource;
use Filament\Resources\Pages\ListRecords;

class ListSquawkAssignments extends ListRecords
{
    use LimitsTableRecordListingOptions;
    protected static string $resource = SquawkAssignmentResource::class;
}
