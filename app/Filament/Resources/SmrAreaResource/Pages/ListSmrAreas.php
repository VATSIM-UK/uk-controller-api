<?php

namespace App\Filament\Resources\SmrAreaResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\SmrAreaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSmrAreas extends ListRecords
{
    use LimitsTableRecordListingOptions;
    protected static string $resource = SmrAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
