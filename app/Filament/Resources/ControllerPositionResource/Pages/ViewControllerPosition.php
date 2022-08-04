<?php

namespace App\Filament\Resources\ControllerPositionResource\Pages;

use App\Filament\Resources\ControllerPositionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewControllerPosition extends ViewRecord
{
    protected static string $resource = ControllerPositionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
