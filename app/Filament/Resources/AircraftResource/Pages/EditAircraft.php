<?php

namespace App\Filament\Resources\AircraftResource\Pages;

use App\Filament\Resources\AircraftResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAircraft extends EditRecord
{
    protected static string $resource = AircraftResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
