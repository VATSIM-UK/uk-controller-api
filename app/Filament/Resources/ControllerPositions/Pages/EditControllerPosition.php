<?php

namespace App\Filament\Resources\ControllerPositions\Pages;

use App\Filament\Resources\ControllerPositions\ControllerPositionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditControllerPosition extends EditRecord
{
    protected static string $resource = ControllerPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
