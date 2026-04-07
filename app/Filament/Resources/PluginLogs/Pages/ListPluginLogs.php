<?php

namespace App\Filament\Resources\PluginLogs\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\PluginLogs\PluginLogResource;
use Filament\Resources\Pages\ListRecords;

class ListPluginLogs extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = PluginLogResource::class;
}
