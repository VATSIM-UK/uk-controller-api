<?php

namespace App\Filament\Resources\DependencyResource\Pages;

use App\Filament\Resources\DependencyResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Resources\Pages\ListRecords;

class ListDependencies extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = DependencyResource::class;
}
