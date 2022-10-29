<?php

namespace App\Filament\Resources\VersionResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\VersionResource;
use Filament\Resources\Pages\ListRecords;

class ListVersions extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = VersionResource::class;
}
