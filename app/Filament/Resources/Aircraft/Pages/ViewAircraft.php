<?php

namespace App\Filament\Resources\Aircraft\Pages;

use App\Filament\Resources\Aircraft\AircraftResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAircraft extends ViewRecord
{
    protected static string $resource = AircraftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
