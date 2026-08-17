<?php

namespace App\Filament\Resources\Sids\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\Sids\SidResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSids extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = SidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
