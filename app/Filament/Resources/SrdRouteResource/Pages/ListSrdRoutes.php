<?php

namespace App\Filament\Resources\SrdRouteResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\SrdRouteResource;
use Filament\Resources\Pages\ListRecords;

class ListSrdRoutes extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = SrdRouteResource::class;
}
