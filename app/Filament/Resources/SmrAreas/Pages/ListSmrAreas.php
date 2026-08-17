<?php

namespace App\Filament\Resources\SmrAreas\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\SmrAreas\SmrAreaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSmrAreas extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = SmrAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
