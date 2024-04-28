<?php

namespace App\Filament\Resources\ControllerPositionResource\Pages;

use App\Filament\Resources\ControllerPositionResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListControllerPositions extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = ControllerPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
