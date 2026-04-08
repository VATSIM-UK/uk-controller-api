<?php

namespace App\Filament\Resources\Dependencies\Pages;

use App\Filament\Resources\Dependencies\DependencyResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Resources\Pages\ListRecords;

class ListDependencies extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = DependencyResource::class;
}
