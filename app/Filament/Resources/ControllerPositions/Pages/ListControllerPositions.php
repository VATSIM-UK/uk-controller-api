<?php

namespace App\Filament\Resources\ControllerPositions\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ControllerPositions\ControllerPositionResource;
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
            CreateAction::make(),
        ];
    }
}
