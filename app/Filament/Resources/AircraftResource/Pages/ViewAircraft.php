<?php

namespace App\Filament\Resources\AircraftResource\Pages;

use App\Filament\Resources\AircraftResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAircraft extends ViewRecord
{
    protected static string $resource = AircraftResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
