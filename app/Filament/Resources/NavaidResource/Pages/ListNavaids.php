<?php

namespace App\Filament\Resources\NavaidResource\Pages;

use App\Filament\Resources\NavaidResource;
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
            Actions\CreateAction::make(),
        ];
    }
}
