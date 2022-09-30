<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Resources\Pages\ListRecords;
use Z3d0X\FilamentLogger\Resources\ActivityResource;

class ListActivities extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = ActivityResource::class;
}
