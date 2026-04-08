<?php

namespace App\Filament\Resources\Navaids\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Navaids\NavaidResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNavaids extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = NavaidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
