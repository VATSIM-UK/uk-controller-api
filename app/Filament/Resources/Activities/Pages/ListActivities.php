<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Resources\Pages\ListRecords;

class ListActivities extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = ActivityResource::class;
}
