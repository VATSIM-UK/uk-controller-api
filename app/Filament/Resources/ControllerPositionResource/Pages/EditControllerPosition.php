<?php

namespace App\Filament\Resources\ControllerPositionResource\Pages;

use App\Filament\Resources\ControllerPositionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditControllerPosition extends EditRecord
{
    protected static string $resource = ControllerPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
