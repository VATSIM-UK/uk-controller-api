<?php

namespace App\Filament\Resources\SidResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\SidResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSids extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = SidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
