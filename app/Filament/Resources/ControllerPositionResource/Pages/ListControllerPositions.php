<?php

namespace App\Filament\Resources\ControllerPositionResource\Pages;

use App\Filament\Resources\ControllerPositionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListControllerPositions extends ListRecords
{
    protected static string $resource = ControllerPositionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
