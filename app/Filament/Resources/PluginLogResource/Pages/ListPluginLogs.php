<?php

namespace App\Filament\Resources\PluginLogResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\PluginLogResource;
use Filament\Resources\Pages\ListRecords;

class ListPluginLogs extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = PluginLogResource::class;
}
