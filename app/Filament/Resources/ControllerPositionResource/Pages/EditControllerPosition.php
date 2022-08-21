<?php

namespace App\Filament\Resources\ControllerPositionResource\Pages;

use App\Filament\Resources\ControllerPositionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditControllerPosition extends EditRecord
{
    protected static string $resource = ControllerPositionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
