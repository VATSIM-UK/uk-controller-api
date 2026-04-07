<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Resources\Pages\ListRecords;
use Jacobtims\FilamentLogger\Resources\ActivityResource;

class ListActivities extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = ActivityResource::class;
}
