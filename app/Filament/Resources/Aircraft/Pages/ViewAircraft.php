<?php

namespace App\Filament\Resources\Aircraft\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\Aircraft\AircraftResource;
use Filament\Pages\Actions;
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
